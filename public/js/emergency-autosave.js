(function (root) {
    'use strict';
    function mount(form, status, options) {
        var queue = new Map(), saved = new Map(), timer = null, active = false, blocked = false, failed = false;
        var waiters = [], allowSubmit = false;
        var ignored = ['_token', 'version', 'id_solicitud', 'classification_method',
            'classification_confirmed', 'save_mode', 'source_triage_id'];
        function controls() {
            return Array.from(form.querySelectorAll('input[name],select[name],textarea[name]'))
                .filter(function (field) { return field.type !== 'hidden' && ignored.indexOf(field.name) === -1; });
        }
        function value(field) {
            if (field.type === 'radio') {
                var selected = controls().find(function (candidate) { return candidate.name === field.name && candidate.checked; });
                return selected ? selected.value : '';
            }
            if (field.type === 'checkbox') return field.checked ? '1' : '0';
            return field.value.trim();
        }
        function snapshot() { saved.clear(); controls().forEach(function (field) { saved.set(field.name, value(field)); }); }
        function show(message, error) {
            if (!status) return;
            status.textContent = message;
            status.classList.toggle('text-danger', !!error);
            status.classList.toggle('text-success', !error && message.indexOf('Guardado') === 0);
        }
        function settle(error) {
            var pending = waiters.splice(0);
            pending.forEach(function (item) { if (error) item.reject(error); else item.resolve(); });
        }
        function hasPending() { return !!timer || active || queue.size > 0; }
        function hasUnsaved() { return controls().some(function (field) { return saved.get(field.name) !== value(field); }); }
        function pump() {
            if (active || blocked) return;
            if (!queue.size) {
                if (hasUnsaved()) {
                    show('Hay datos sin guardar. Corrija el campo indicado o revise la conexión.', true);
                    settle(new Error('Hay datos sin guardar.'));
                } else { settle(); if (!failed && options.onIdle) options.onIdle(); }
                return;
            }
            if (!form.dataset.autosaveUrl || !/^\d+$/.test(form.elements.version.value)) {
                blocked = true; show('No se pudo guardar: falta el episodio vigente.', true);
                settle(new Error('Sin episodio vigente')); return;
            }
            var item = queue.entries().next().value;
            queue.delete(item[0]); active = true; show('Guardando…');
            var body = new URLSearchParams({
                _token:form.elements._token.value, version:form.elements.version.value,
                field:item[0], value:item[1]
            });
            fetch(form.dataset.autosaveUrl, {method:'POST', credentials:'same-origin',
                headers:{Accept:'application/json', 'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8',
                    'X-Requested-With':'XMLHttpRequest'}, body:body.toString()})
                .then(function (response) { return response.json().then(function (data) {
                    if (!response.ok || !Number.isInteger(data.version)) throw {status:response.status, data:data};
                    return data;
                }); })
                .then(function (data) {
                    form.elements.version.value = data.version;
                    saved.set(item[0], item[1]);
                    failed = false;
                    show('Guardado automáticamente a las ' + data.saved_at);
                    if (options.onSaved) options.onSaved(data);
                })
                .catch(function (failure) {
                    failed = true;
                    var data = failure.data || {};
                    var message = data.errors ? Object.values(data.errors).flat().join(' ')
                        : (data.message || 'No se pudo guardar este campo. Revise la conexión.');
                    show('Sin guardar: ' + message, true);
                    if (failure.status === 409 || failure.status === 403) blocked = true;
                    settle(new Error(message));
                })
                .finally(function () { active = false; if (!blocked) pump(); });
        }
        function schedule(field, immediate) {
            if (!field || ignored.indexOf(field.name) !== -1 || field.type === 'hidden') return;
            if (field.validity && (field.validity.badInput || field.validity.typeMismatch || field.validity.rangeOverflow
                || field.validity.rangeUnderflow || field.validity.stepMismatch || field.validity.tooLong)) {
                show('Corrija este dato para guardarlo automáticamente.', true); return;
            }
            var current = value(field);
            if (current === '' && /^patient\[(nombre1|apellido1|sexo|fecha_nacimiento)\]$/.test(field.name)) {
                show('Complete el dato de identidad antes de guardarlo.', true); return;
            }
            if (saved.get(field.name) === current && !queue.has(field.name)) return;
            blocked = false; failed = false;
            queue.set(field.name, current);
            clearTimeout(timer);
            timer = setTimeout(function () { timer = null; pump(); }, immediate ? 0 : 1500);
            show('Cambios pendientes de guardar…');
        }
        function flush() {
            clearTimeout(timer); timer = null;
            if (!hasPending()) return blocked || hasUnsaved()
                ? Promise.reject(new Error('Hay cambios sin guardar.')) : Promise.resolve();
            return new Promise(function (resolve, reject) { waiters.push({resolve:resolve, reject:reject}); pump(); });
        }
        function reset() {
            clearTimeout(timer); timer = null; queue.clear(); blocked = false; failed = false; snapshot();
            show('Los cambios se guardan al terminar de escribir.');
        }
        function hydrate(draft) {
            Object.keys(draft || {}).forEach(function (name) {
                if (ignored.indexOf(name) !== -1) return;
                var fields = Array.from(form.querySelectorAll('[name]')).filter(function (field) { return field.name === name; });
                fields.forEach(function (field) {
                    if (field.type === 'radio') field.checked = field.value === String(draft[name]);
                    else if (field.type === 'checkbox') field.checked = String(draft[name]) === '1';
                    else field.value = draft[name] == null ? '' : draft[name];
                });
            });
            if (root.jQuery) root.jQuery(form).find('select').trigger('change.select2');
            form.dispatchEvent(new Event('emergency:classification-reset'));
            reset();
        }
        if (root.jQuery) {
            root.jQuery(form).on('input.emergencyAutosave change.emergencyAutosave', 'input[name],select[name],textarea[name]', function (event) {
                schedule(event.target, event.type === 'change');
            });
            root.jQuery(form).on('focusout.emergencyAutosave', 'input[name],select[name],textarea[name]', function (event) {
                if (queue.has(event.target.name)) { clearTimeout(timer); timer = null; pump(); }
            });
        }
        form.addEventListener('submit', function (event) {
            if (allowSubmit) { allowSubmit = false; return; }
            if (!hasPending() && !hasUnsaved()) return;
            event.preventDefault(); event.stopImmediatePropagation();
            var button = event.submitter;
            flush().then(function () {
                allowSubmit = true;
                form.requestSubmit(button || undefined);
            }).catch(function () { show('Hay cambios sin guardar. Revise el aviso antes de confirmar.', true); });
        }, true);
        snapshot(); reset();
        return {flush:flush, reset:reset, hydrate:hydrate, hasPending:hasPending, hasUnsaved:hasUnsaved, show:show};
    }
    root.EmergencyAutosave = {mount:mount};
})(window);
