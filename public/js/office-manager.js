(function () {
    'use strict';
    const manager = document.getElementById('office-manager');
    if (!manager) return;
    manager.addEventListener('click', function (event) {
        const button = event.target.closest('.ofm-organize');
        if (!button) return;
        const form = document.getElementById('office-organize-form');
        form.action = button.dataset.url;
        document.getElementById('office-organize-title').value = button.dataset.title;
        document.getElementById('office-organize-folder').value = button.dataset.folder || '';
        document.getElementById('office-organize-version').value = button.dataset.version;
        button.closest('details').open = false;
        window.jQuery('#officeOrganize').modal('show');
    });
    const upload = document.getElementById('office-upload-form');
    upload.addEventListener('submit', function (event) {
        const input = document.getElementById('office-upload');
        const files = Array.from(input.files);
        const status = document.getElementById('office-upload-status');
        if (files.length > 10 || files.some(function (file) { return file.size > Number(input.dataset.fileLimit); }) || files.reduce(function (sum, file) { return sum + file.size; }, 0) > Number(input.dataset.totalLimit)) {
            event.preventDefault(); status.textContent = 'La selección supera los límites indicados. Reduce el tamaño o divide los archivos en varias cargas.'; return;
        }
        status.textContent = 'Subiendo archivos. Mantén esta ventana abierta…';
        upload.querySelector('[type=submit]').disabled = true;
    });
    document.addEventListener('click', function (event) {
        manager.querySelectorAll('details[open]').forEach(function (details) { if (!details.contains(event.target)) details.open = false; });
    });
})();
