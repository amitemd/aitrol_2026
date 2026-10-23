(function () {
    'use strict';
    const root = document.getElementById('office-suite');
    if (!root) return;
    const title = document.getElementById('suite-title'), status = document.getElementById('suite-status'), error = document.getElementById('suite-error');
    let version = Number(root.dataset.version), dirty = false, revision = 0, timer, pending = null, conflict = false;
    const suite = window.OfficeSuite = {data: JSON.parse(document.getElementById('suite-data').textContent), kind: root.dataset.kind,
        error: function (message) { error.textContent = message; error.hidden = false; },
        changed: function () { dirty = true; revision++; status.textContent = 'Cambios sin guardar'; clearTimeout(timer); if (!conflict) timer = setTimeout(save, 1500); }};
    async function save() {
        clearTimeout(timer);
        if (pending) { await pending; if (dirty && !conflict) return save(); return !dirty; }
        if (!dirty) return true;
        if (conflict) return false;
        if (!title.value.trim()) { suite.error('Escribe un nombre para guardar el archivo.'); return false; }
        const snapshot = revision;
        status.textContent = 'Guardando…'; error.hidden = true;
        document.getElementById('suite-save').disabled = true;
        pending = (async function () {
            try {
                const response = await fetch(root.dataset.saveUrl, {method: 'PUT', credentials: 'same-origin', headers: {'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-TOKEN': root.dataset.token}, body: JSON.stringify({title: title.value.trim(), content: JSON.stringify(suite.data), version: version})});
                const data = await response.json().catch(function () { return {}; });
                if (!response.ok) {
                    conflict = response.status === 409;
                    throw new Error(response.status === 401 || response.status === 419 ? 'La sesión expiró. Descarga una copia de seguridad antes de recargar.' : (data.errors ? Object.values(data.errors).flat().join(' ') : data.message || 'No se pudo guardar. Inténtalo de nuevo.'));
                }
                version = data.version; dirty = revision !== snapshot;
                status.textContent = dirty ? 'Cambios sin guardar' : 'Todos los cambios guardados · ' + new Date().toLocaleTimeString('es');
                if (dirty) timer = setTimeout(save, 700);
                return true;
            } catch (exception) { suite.error(exception.message); status.textContent = 'Hay cambios sin guardar'; return false; }
            finally { document.getElementById('suite-save').disabled = conflict; pending = null; }
        })();
        return pending;
    }
    suite.save = save;
    title.addEventListener('input', suite.changed);
    document.getElementById('suite-save').addEventListener('click', save);
    document.addEventListener('keydown', function (event) { if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') { event.preventDefault(); save(); } });
    window.addEventListener('beforeunload', function (event) { if (dirty || pending) { event.preventDefault(); event.returnValue = ''; } });
    document.getElementById('suite-export').addEventListener('click', async function () {
        let ready = await save();
        while (ready && dirty) ready = await save();
        if (ready) {
            const link = document.createElement('a'); link.href = root.dataset.downloadUrl; link.download = ''; document.body.appendChild(link); link.click(); link.remove();
        }
    });
    document.getElementById('suite-backup').addEventListener('click', function () {
        const blob = new Blob([JSON.stringify({aitrol: 1, kind: suite.kind, title: title.value, data: suite.data})], {type: 'application/json'});
        const url = URL.createObjectURL(blob), link = document.createElement('a'); link.href = url; link.download = 'Copia-oficina.aitrol.json'; document.body.appendChild(link); link.click(); link.remove(); setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    });
})();
