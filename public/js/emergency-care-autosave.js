(function (root) {
    'use strict';

    function mount(panel) {
        if (panel.emergencyCareAutosave) return panel.emergencyCareAutosave;
        var states = [], queue = [], active = false;
        var pause = 700, maximumWait = 5000;

        function changed(state) { return state.field.value !== state.saved; }
        function show(state, message, error) {
            if (!state.status) return;
            state.status.textContent = message;
            state.status.classList.toggle('is-error', !!error);
        }
        function cancelTimers(state) {
            root.clearTimeout(state.timer);
            root.clearTimeout(state.maximumTimer);
            state.timer = state.maximumTimer = null;
        }
        function enqueue(state) {
            cancelTimers(state);
            if (state.blocked || state.composing) return;
            if (queue.indexOf(state) === -1) queue.push(state);
            pump();
        }
        function messageFor(response, data) {
            if (response.status === 401 || response.status === 419) return 'La sesión venció. Copie sus cambios antes de volver a iniciar sesión.';
            if (response.status === 403) return 'No tiene permiso para guardar. El texto permanece en pantalla.';
            if (data.errors) return Object.keys(data.errors).map(function (key) {
                return [].concat(data.errors[key]).join(' ');
            }).join(' ');
            return data.message || 'No se pudo guardar. Revise la conexión y pulse Guardar ahora.';
        }
        function pump() {
            if (active) return;
            var state;
            while (queue.length) {
                var candidate = queue.shift();
                if (!candidate.blocked && !candidate.composing && changed(candidate)) { state = candidate; break; }
            }
            if (!state) return;
            var baseId = Number(state.field.dataset.baseId);
            if (!panel.dataset.saveUrl || !panel.dataset.csrfToken || !Number.isInteger(baseId) || baseId < 0) {
                state.blocked = true;
                show(state, 'No se pudo iniciar el guardado. Conserve el texto y recargue la página.', true);
                pump(); return;
            }
            var value = state.field.value;
            state.saving = active = true;
            state.failed = false;
            if (state.button) state.button.disabled = true;
            show(state, 'Guardando…');
            var succeeded = false;
            root.fetch(panel.dataset.saveUrl, {
                method: 'POST', credentials: 'same-origin',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json',
                    'X-CSRF-TOKEN': panel.dataset.csrfToken, 'X-Requested-With': 'XMLHttpRequest'},
                body: JSON.stringify({section: state.key, content: value, base_entry_id: baseId})
            }).then(function (response) {
                // Una sesión vencida o un error del servidor también puede devolver HTML.
                return response.json().catch(function () { return {}; }).then(function (data) {
                    if (!data || typeof data !== 'object') data = {};
                    if (!response.ok) throw {status: response.status, message: messageFor(response, data)};
                    if (!data || !Number.isInteger(data.entry_id) || data.entry_id < 0
                        || (data.entry_id === 0 && value.trim() !== '')) {
                        throw {message: 'No se pudo confirmar el guardado. Conserve el texto y revise la conexión.'};
                    }
                    return data;
                });
            }).then(function (data) {
                succeeded = true;
                state.field.dataset.baseId = String(data.entry_id);
                state.saved = value;
                state.savedMessage = data.saved_at ? 'Guardado automáticamente · ' + data.saved_at
                    + (data.recorded_by ? ' · ' + data.recorded_by : '') : 'Sin cambios pendientes';
                show(state, changed(state) ? 'Cambios pendientes de guardar…' : state.savedMessage);
            }).catch(function (error) {
                state.failed = true;
                state.blocked = [401, 403, 409, 419].indexOf(error.status) !== -1;
                cancelTimers(state);
                queue = queue.filter(function (item) { return item !== state; });
                show(state, ((!error.name && error.message) || 'No se pudo guardar. Revise la conexión y pulse Guardar ahora.')
                    + (error.status === 409 ? ' El texto permanece aquí; cópielo antes de recargar.' : ''), true);
            }).finally(function () {
                state.saving = active = false;
                if (state.button) state.button.disabled = state.blocked;
                // La respuesta confirma únicamente el texto enviado, nunca una edición posterior.
                if (succeeded && changed(state) && !state.timer && !state.composing) enqueue(state);
                pump();
            });
        }
        function schedule(state) {
            if (state.blocked) return;
            state.failed = false;
            root.clearTimeout(state.timer);
            if (!changed(state) && !state.saving) {
                cancelTimers(state);
                show(state, state.savedMessage);
                return;
            }
            show(state, state.saving ? 'Guardando… hay cambios nuevos pendientes' : 'Cambios pendientes de guardar…');
            if (state.composing) return;
            state.timer = root.setTimeout(function () { enqueue(state); }, pause);
            if (!state.maximumTimer) state.maximumTimer = root.setTimeout(function () { enqueue(state); }, maximumWait);
        }

        Array.from(panel.querySelectorAll('textarea[data-care-section]')).forEach(function (field) {
            if (field.readOnly || field.disabled) return;
            var key = field.dataset.careSection;
            var state = {key: key, field: field, saved: field.value, saving: false, failed: false,
                blocked: false, composing: false, timer: null, maximumTimer: null,
                status: panel.querySelector('#care-status-' + key),
                button: panel.querySelector('[data-save-section="' + key + '"]')};
            state.savedMessage = state.status ? state.status.textContent : 'Sin cambios pendientes';
            states.push(state);
            field.addEventListener('input', function () { schedule(state); });
            field.addEventListener('change', function () { enqueue(state); });
            field.addEventListener('blur', function () { enqueue(state); });
            field.addEventListener('compositionstart', function () { state.composing = true; cancelTimers(state); });
            field.addEventListener('compositionend', function () { state.composing = false; schedule(state); });
            if (state.button) state.button.addEventListener('click', function () { enqueue(state); });
        });
        function hasUnsaved() { return states.some(function (state) { return state.saving || changed(state); }); }
        function flush() { states.forEach(function (state) { if (changed(state)) enqueue(state); }); }
        root.addEventListener('online', function () {
            states.forEach(function (state) { if (state.failed && !state.blocked) enqueue(state); });
        });
        root.addEventListener('beforeunload', function (event) {
            if (hasUnsaved()) { event.preventDefault(); event.returnValue = ''; }
        });
        panel.emergencyCareAutosave = {hasUnsaved: hasUnsaved, flush: flush};
        return panel.emergencyCareAutosave;
    }

    function boot() {
        root.document.querySelectorAll('[data-emergency-care]').forEach(mount);
    }
    root.EmergencyCareAutosave = {mount: mount};
    // La vista inserta este script después de los campos. No espera scripts del layout/CDN.
    boot();
    if (root.document.readyState === 'loading') root.document.addEventListener('DOMContentLoaded', boot, {once: true});
})(window);
