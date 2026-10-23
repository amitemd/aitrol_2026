(function () {
    'use strict';
    const root = document.getElementById('office-workspace');
    if (!root) return;
    const title = document.getElementById('office-title');
    const status = document.getElementById('office-status');
    const error = document.getElementById('office-error');
    const saveButton = document.getElementById('office-save');
    let editor, version = Number(root.dataset.version), dirty = false, saving = false, timer, revision = 0, conflict = false;
    function fail(message) { error.textContent = message; error.hidden = false; }
    function changed() {
        dirty = true; revision++; clearTimeout(timer);
        status.textContent = 'Cambios sin guardar';
        if (!conflict) timer = setTimeout(save, 1600);
    }
    async function save() {
        clearTimeout(timer);
        if (!editor || saving || conflict || !dirty) return;
        if (!title.value.trim()) { fail('Escribe un nombre para guardar el documento.'); title.focus(); return; }
        const currentRevision = revision;
        saving = true; saveButton.disabled = true; status.textContent = 'Guardando…'; error.hidden = true;
        try {
            const response = await fetch(root.dataset.saveUrl, {
                method: 'PUT', credentials: 'same-origin',
                headers: {'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': root.dataset.token},
                body: JSON.stringify({title: title.value.trim(), content: editor.getContent(), version: version})
            });
            const data = await response.json().catch(function () { return {}; });
            if (!response.ok) {
                conflict = response.status === 409;
                throw new Error(response.status === 419 || response.status === 401 ? 'La sesión expiró. Descarga una copia antes de volver a iniciar sesión.' : (data.message || 'No se pudo guardar. Conserva esta pestaña abierta y vuelve a intentarlo.'));
            }
            version = data.version;
            dirty = revision !== currentRevision;
            status.textContent = dirty ? 'Cambios sin guardar' : 'Todos los cambios guardados · ' + new Date().toLocaleTimeString('es');
            if (dirty) timer = setTimeout(save, 800);
        } catch (exception) {
            status.textContent = 'No se han guardado los cambios';
            fail(exception.message);
        } finally { saving = false; saveButton.disabled = conflict; }
    }
    window.addEventListener('beforeunload', function (event) {
        if (dirty || saving) { event.preventDefault(); event.returnValue = ''; }
    });
    title.addEventListener('input', changed);
    saveButton.addEventListener('click', save);
    document.addEventListener('keydown', function (event) {
        if ((event.ctrlKey || event.metaKey) && event.key.toLowerCase() === 's') { event.preventDefault(); save(); }
    });
    let initialized = false;
    function startupFailure() {
        status.textContent = 'No se pudo iniciar el editor';
        fail('No se pudo cargar el editor. Recarga la página. Si continúa, comprueba que los archivos del editor estén disponibles en el servidor.');
    }
    if (!window.tinymce) { startupFailure(); return; }
    const startupTimer = setTimeout(function () { if (!initialized) startupFailure(); }, 12000);
    tinymce.addI18n('es', {
        'File':'Archivo','Edit':'Editar','View':'Ver','Insert':'Insertar','Format':'Formato','Tools':'Herramientas','Table':'Tabla','Help':'Ayuda',
        'Undo':'Deshacer','Redo':'Rehacer','Bold':'Negrita','Italic':'Cursiva','Underline':'Subrayado','Strikethrough':'Tachado','Formats':'Formatos',
        'Paragraph':'Párrafo','Headings':'Encabezados','Heading 1':'Título 1','Heading 2':'Título 2','Heading 3':'Título 3',
        'Align left':'Alinear a la izquierda','Align center':'Centrar','Align right':'Alinear a la derecha','Justify':'Justificar',
        'Bullet list':'Lista con viñetas','Numbered list':'Lista numerada','Increase indent':'Aumentar sangría','Decrease indent':'Reducir sangría',
        'Font sizes':'Tamaños de letra','Fonts':'Fuentes','Text color':'Color del texto','Background color':'Color de fondo',
        'Remove formatting':'Quitar formato','Insert table':'Insertar tabla','Row':'Fila','Column':'Columna','Cell':'Celda','Delete table':'Eliminar tabla',
        'Clear formatting':'Quitar formato','Blocks':'Estilos','{0} words':'{0} palabras','Rich Text Area. Press ALT-0 for help.':'Área de edición. Pulsa ALT-0 para obtener ayuda.',
        'More...':'Más herramientas…',
        'Table properties':'Propiedades de tabla','Insert row before':'Insertar fila antes','Insert row after':'Insertar fila después','Delete row':'Eliminar fila',
        'Insert column before':'Insertar columna antes','Insert column after':'Insertar columna después','Delete column':'Eliminar columna',
        'Merge cells':'Combinar celdas','Split cell':'Dividir celda','Print':'Imprimir','Preview':'Vista previa','Fullscreen':'Pantalla completa',
        'Find and replace':'Buscar y reemplazar','Find':'Buscar','Replace':'Reemplazar','Replace all':'Reemplazar todo','Close':'Cerrar',
        'Save':'Guardar','Cancel':'Cancelar','Ok':'Aceptar','Insert/edit link':'Insertar/editar enlace','Link':'Enlace','Text to display':'Texto para mostrar',
        'Cut':'Cortar','Copy':'Copiar','Paste':'Pegar','Select all':'Seleccionar todo','Word count':'Contar palabras','Words':'Palabras'
    });
    tinymce.init({
        base_url: root.dataset.editorBase, suffix: '.min',
        selector: '#office-content', language: 'es', height: Math.max(500, window.innerHeight - 265),
        plugins: 'lists link table paste searchreplace wordcount fullscreen print preview',
        menubar: 'edit insert view format table tools', branding: false,
        toolbar: 'undo redo | formatselect fontselect fontsizeselect | bold italic underline forecolor backcolor | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | table link officeimage | removeformat searchreplace fullscreen',
        toolbar_mode: 'sliding', browser_spellcheck: true, paste_data_images: true,
        automatic_uploads: false, convert_urls: false,
        invalid_elements: 'script,style,iframe,object,embed,svg,math,template,form,input,button,link,meta,base',
        content_style: 'html{background:#eaf0f6}body{box-sizing:border-box;max-width:794px;min-height:1050px;margin:22px auto;padding:65px;background:#fff;box-shadow:0 2px 12px #23394c15;font:12pt Arial,sans-serif;color:#222;line-height:1.6}img{max-width:100%;height:auto}table{border-collapse:collapse;max-width:100%}td,th{padding:6px} @media(max-width:650px){body{padding:24px;margin:10px}} @media print{html{background:#fff}body{max-width:none;min-height:0;margin:0;padding:0;box-shadow:none}@page{size:A4;margin:20mm}}',
        setup: function (instance) {
            editor = instance;
            instance.ui.registry.addButton('officeimage', {icon: 'image', tooltip: 'Insertar imagen desde el equipo', onAction: function () { document.getElementById('office-image').click(); }});
            instance.on('init', function () {
                initialized = true; clearTimeout(startupTimer); error.hidden = true;
                status.textContent = 'Todos los cambios guardados';
                ['office-save', 'office-print', 'office-download'].forEach(function (id) { document.getElementById(id).disabled = false; });
                instance.on('input change undo redo', changed);
                instance.addShortcut('meta+s', 'Guardar documento', save);
            });
        }
    }).catch(startupFailure);
    document.getElementById('office-image').addEventListener('change', function (event) {
        const file = event.target.files[0]; event.target.value = '';
        if (!file) return;
        if (!/^image\/(png|jpeg|gif|webp)$/.test(file.type) || file.size > 1000000) { fail('Selecciona una imagen PNG, JPG, GIF o WebP de hasta 1 MB.'); return; }
        const reader = new FileReader();
        reader.onload = function () { editor.insertContent('<img src="' + reader.result + '" alt="Imagen">'); changed(); };
        reader.onerror = function () { fail('No se pudo leer la imagen.'); };
        reader.readAsDataURL(file);
    });
    document.getElementById('office-print').addEventListener('click', function () { editor.execCommand('mcePrint'); });
    document.getElementById('office-download').addEventListener('click', function () {
        const escape = function (text) { const span = document.createElement('span'); span.textContent = text; return span.innerHTML; };
        const html = '<!doctype html><html lang="es"><meta charset="utf-8"><title>' + escape(title.value) + '</title><style>body{max-width:794px;margin:35px auto;padding:25px;font:12pt Arial;line-height:1.6}img{max-width:100%}table{border-collapse:collapse}td,th{padding:6px}</style><body>' + editor.getContent() + '</body></html>';
        const url = URL.createObjectURL(new Blob([html], {type: 'text/html;charset=utf-8'}));
        const link = document.createElement('a'); link.href = url; link.download = (title.value.replace(/[<>:"/\\|?*\x00-\x1f]/g, '_').slice(0, 120) || 'Documento') + '.html';
        document.body.appendChild(link); link.click(); link.remove(); setTimeout(function () { URL.revokeObjectURL(url); }, 1000);
    });
})();
