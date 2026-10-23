(function ($) {
    'use strict';
    var form = document.getElementById('emergency-progress-form');
    if (!form) return;
    var saving = false, leaving = false;
    var error = document.getElementById('ep-error');
    function notifyParent(mode) {
        if (window.parent !== window) window.parent.postMessage({type:'emergency-progress-saved', mode:mode}, window.location.origin);
    }
    var autosave = window.EmergencyAutosave.mount(form, document.getElementById('ep-autosave-status'), {
        onSaved:function () { notifyParent('AUTOSAVE'); }
    });
    function reveal(field) {
        var details = field.closest('details'); if (details) details.open = true;
        field.focus();
    }
    form.addEventListener('submit', function (event) {
        event.preventDefault();
        if (saving) return;
        var mode = event.submitter ? event.submitter.value : 'DRAFT';
        var fields = Array.from(form.querySelectorAll('input,select,textarea'));
        var constraints = fields.map(function (field) { return {required:field.required, min:field.getAttribute('minlength')}; });
        if (mode === 'DRAFT') fields.forEach(function (field) { field.required = false; field.removeAttribute('minlength'); });
        var invalid = fields.find(function (field) { return !field.validity.valid; });
        if (invalid) { reveal(invalid); invalid.reportValidity(); }
        fields.forEach(function (field, index) {
            field.required = constraints[index].required;
            if (constraints[index].min !== null) field.setAttribute('minlength', constraints[index].min);
        });
        if (invalid) return;
        var payload = $(form).serialize() + '&save_mode=' + mode;
        var controls = Array.from(form.querySelectorAll('input,select,textarea,button'));
        controls.forEach(function (control) { control.disabled = true; });
        saving = true; error.hidden = true;
        $.ajax({url:form.action, method:'POST', data:payload, dataType:'json', headers:{Accept:'application/json'}})
            .done(function (data) {
                if (!data.url || typeof data.version !== 'number') { error.textContent = 'No se pudo confirmar el guardado. Revise el episodio antes de reintentar.'; error.hidden = false; return; }
                notifyParent(mode);
                leaving = true; window.location.assign(data.url);
            }).fail(function (xhr) {
                var body = xhr.responseJSON || {};
                error.textContent = body.errors ? Object.values(body.errors).map(function (messages) { return messages.join(' '); }).join('\n') : (body.message || 'No se pudo guardar. Sus datos siguen en pantalla.');
                error.hidden = false; error.focus();
            }).always(function () {
                if (!leaving) { saving = false; controls.forEach(function (control) { control.disabled = false; }); }
            });
    });
    window.addEventListener('beforeunload', function (event) { if ((autosave.hasUnsaved() || autosave.hasPending()) && !leaving) { event.preventDefault(); event.returnValue = ''; } });
})(jQuery);
