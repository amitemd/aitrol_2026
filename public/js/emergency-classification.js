(function (root) {
    'use strict';
    function glasgow(values) {
        var maxima = [4, 5, 6];
        if (values.length !== 3 || values.some(function (value, i) {
            return !/^[1-6]$/.test(String(value)) || Number(value) > maxima[i];
        })) return null;
        return values.reduce(function (total, value) { return total + Number(value); }, 0);
    }
    function glasgowRange(total) {
        if (!Number.isInteger(total) || total < 3 || total > 15) return null;
        return total <= 8 ? '3-8' : (total <= 12 ? '9-12' : '13-15');
    }
    // Local suggestion requested by the clinic; this is not a Manchester discriminant.
    function gcsTableLevel(total) {
        if (!Number.isInteger(total) || total < 3 || total > 15) return null;
        return total <= 8 ? 1 : total <= 12 ? 2 : total <= 14 ? 3 : 4;
    }
    function shouldAutoConfirm(level) {
        level = Number(level);
        return level >= 1 && level <= 3;
    }
    if (typeof module === 'object' && module.exports) module.exports = {glasgow:glasgow, glasgowRange:glasgowRange,
        gcsTableLevel:gcsTableLevel, demoPriorityLevel:gcsTableLevel, shouldAutoConfirm:shouldAutoConfirm};
    if (!root.document) return;
    root.document.querySelectorAll('[data-emergency-classification]').forEach(function (panel) {
        var form = panel.closest('form');
        var responses = Array.from(panel.querySelectorAll('[data-gcs-component]'));
        var confirm = panel.querySelector('[data-classification-confirm]');
        var demo = panel.dataset.gcsDemo === '1';
        var lastSuggestedTotal, autoSelected = null, settingSuggestion = false;
        if (demo) {
            var demoNote = panel.querySelector('[data-gcs-demo-note]');
            if (demoNote) demoNote.hidden = false;
        }
        function changePriority(input) {
            settingSuggestion = true;
            if (root.jQuery) root.jQuery(input).trigger('change');
            else input.dispatchEvent(new Event('change', {bubbles:true}));
            settingSuggestion = false;
        }
        function updateSuggestedPriority(total, glasgowChanged) {
            var selected = panel.querySelector('[data-clinical-priority]:checked');
            // A saved level remains intact when a draft is reopened. If it still
            // matches Glasgow, treat it as the automatic suggestion.
            if (!glasgowChanged && selected) {
                lastSuggestedTotal = total;
                autoSelected = Number(selected.dataset.level) === gcsTableLevel(total) ? selected : null;
                return;
            }
            if (total === lastSuggestedTotal) return;
            lastSuggestedTotal = total;
            var level = gcsTableLevel(total);
            var target = level ? panel.querySelector('[data-clinical-priority][data-level="' + level + '"]') : null;
            if (target) {
                autoSelected = target;
                if (selected !== target) {
                    target.checked = true;
                    changePriority(target);
                }
            } else if (autoSelected && autoSelected.checked) {
                autoSelected.checked = false;
                var cleared = autoSelected;
                autoSelected = null;
                changePriority(cleared);
            }
        }
        function update(glasgowChanged) {
            var values = responses.map(function (field) { return field.value; });
            var total = glasgow(values), nt = values.indexOf('NT') !== -1;
            updateSuggestedPriority(total, !!glasgowChanged);
            var score = total === null ? (nt ? 'No calculable' : (values.some(Boolean) ? 'Incompleto' : 'Sin evaluar')) : total + ' / 15';
            panel.querySelector('[data-gcs-total]').textContent = score;
            var range = glasgowRange(total);
            panel.querySelectorAll('[data-gcs-range]').forEach(function (item) {
                var active = item.dataset.gcsRange === range;
                item.classList.toggle('is-current', active);
                if (active) item.setAttribute('aria-current', 'true'); else item.removeAttribute('aria-current');
            });
            var rangeOutput = panel.querySelector('[data-gcs-range-label]');
            if (rangeOutput) rangeOutput.textContent = range ? 'Rango de Glasgow: ' + range.replace('-', '–') + ' puntos' : 'Rango pendiente: complete las tres respuestas evaluables.';
            var detail = responses.map(function (field) { return field.dataset.gcsCode + ' ' + (field.value || '—'); }).join(' · ');
            panel.querySelector('[data-gcs-detail]').textContent = detail + (nt ? ' · Sin total: existe una respuesta no evaluable.' : '');
            panel.querySelector('[data-gcs-note-wrap]').hidden = !nt && !panel.querySelector('[data-gcs-note]').value;
            panel.querySelector('[data-gcs-note]').required = nt;
            var selected = panel.querySelector('[data-clinical-priority]:checked');
            var result = panel.querySelector('[data-classification-result]');
            result.className = 'ec-result' + (selected ? ' ec-level-' + selected.dataset.level : '');
            panel.dataset.gcsSuggested = autoSelected && autoSelected.checked ? '1' : '0';
            // Glasgow only confirms urgent suggestions automatically: yellow,
            // orange and red. Green and blue require an explicit clinical check.
            if (!demo && panel.dataset.gcsSuggested === '1' && selected && shouldAutoConfirm(selected.dataset.level)) {
                confirm.checked = true;
            }
            panel.querySelector('[data-classification-label]').textContent = selected
                ? (demo ? 'Simulación · ' : (panel.dataset.gcsSuggested === '1' ? 'Sugerencia por Glasgow · ' : '')) + selected.dataset.label
                : 'Pendiente de clasificar';
            panel.querySelector('[data-classification-time]').textContent = selected
                ? (selected.dataset.time === '0' ? 'Atención inmediata' : 'Objetivo de valoración: ' + selected.dataset.time + ' minutos')
                : 'Seleccione un nivel después de valorar al paciente.';
            form.querySelectorAll('[data-gcs-summary]').forEach(function (output) { output.textContent = score + ' · ' + detail; });
        }
        // Any clinical edit invalidates the previous confirmation, including edits outside the panel.
        function changed(event) {
            if (event.target !== confirm) confirm.checked = false;
            if (!settingSuggestion && event.target.matches('[data-clinical-priority]')) autoSelected = null;
            update(event.target.matches('[data-gcs-component]'));
        }
        // Select2 emits jQuery change events, which native addEventListener does not receive.
        // Deferred loading binds to the final jQuery instance from the hospital layout.
        if (root.jQuery) root.jQuery(form).on('input.emergencyClassification change.emergencyClassification', changed);
        else { form.addEventListener('input', changed); form.addEventListener('change', changed); }
        function reset() {
            confirm.checked = false;
            lastSuggestedTotal = undefined;
            autoSelected = null;
            if (root.jQuery) root.jQuery(panel).find('select').trigger('change.select2');
            update();
        }
        form.addEventListener('reset', function () { setTimeout(reset, 0); });
        form.addEventListener('emergency:classification-reset', reset);
        update();
    });
})(typeof window !== 'undefined' ? window : globalThis);
