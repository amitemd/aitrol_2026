(function () {
    'use strict';

    var i18n = window.AitrolV3I18n || {};
    var moduleDescriptions = i18n.module_descriptions || {
        activosfijos: 'Administra el registro, clasificación, ubicación y depreciación de los activos de la empresa.',
        cajabancos: 'Gestiona movimientos, conciliaciones y operaciones de caja y bancos.',
        clientes: 'Administra la información comercial, tributaria y financiera de tus clientes.',
        compras: 'Gestiona órdenes, compras, gastos y documentos asociados a proveedores.',
        configuraciones: 'Administra los parámetros, usuarios y opciones generales del sistema.',
        configuracionessistema: 'Configura los catálogos y parámetros institucionales del sistema.',
        contabilidad: 'Consulta y administra asientos, planes de cuenta y procesos contables.',
        guiaderemision: 'Gestiona transportistas, destinos y guías de remisión emitidas.',
        importaciones: 'Controla los procesos, documentos y costos asociados a importaciones.',
        inventario: 'Administra productos, existencias, bodegas y movimientos de inventario.',
        proveedores: 'Administra la información comercial, tributaria y financiera de proveedores.',
        recursoshumanos: 'Gestiona colaboradores, roles y configuraciones de talento humano.',
        reportes: 'Consulta indicadores e informes consolidados para la toma de decisiones.',
        tributario: 'Gestiona documentos electrónicos y obligaciones tributarias.',
        utilidades: 'Accede a herramientas y procesos complementarios del sistema.',
        ventas: 'Gestiona facturación, documentos electrónicos y operaciones de venta.'
    };

    function normalizeKey(value) {
        return (value || '')
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]/g, '');
    }

    function getModuleLabel(shell) {
        var activeHeaderLink = document.querySelector('.main-header .nav-link.active');
        var label = activeHeaderLink ? activeHeaderLink.textContent.trim() : '';

        return label || shell.getAttribute('data-module') || 'Gestión';
    }

    function getModuleIconClass() {
        var icons = document.querySelectorAll('.main-sidebar .nav-link.active .nav-icon');
        var icon = icons.length ? icons[icons.length - 1] : null;

        return icon ? icon.className : 'fas fa-layer-group';
    }

    function findPrimaryAction(content) {
        var candidates = content.querySelectorAll('.card-header .btn-primary, .card-header .btn-info');

        for (var index = 0; index < candidates.length; index += 1) {
            var text = candidates[index].textContent.trim().toLowerCase();

            if (/agregar|nuevo|nueva|crear|registrar|add|new|create|register|adicionar|novo|nova|criar/.test(text)) {
                return candidates[index];
            }
        }

        return null;
    }

    function removeEmptyParents(element, boundary) {
        var parent = element;

        while (parent && parent !== boundary) {
            var nextParent = parent.parentElement;

            if (!parent.textContent.trim() && !parent.querySelector('button, a, input, select, textarea')) {
                parent.remove();
            }

            parent = nextParent;
        }
    }

    function buildPageHero(shell, header, content) {
        var title = header.querySelector('h1');
        var container = content.querySelector(':scope > .container-fluid') || content.querySelector('.container-fluid');

        if (!title || !container || content.querySelector('.v3-page-hero')) {
            return;
        }

        var titleColumn = title.parentElement;
        var moduleLabel = getModuleLabel(shell);
        var descriptionKey = normalizeKey(shell.getAttribute('data-module') || moduleLabel);
        var description = moduleDescriptions[descriptionKey] || moduleDescriptions.default || i18n.section_description || '';
        var hero = document.createElement('div');
        var copy = document.createElement('div');
        var icon = document.createElement('span');
        var text = document.createElement('div');
        var eyebrow = document.createElement('div');
        var subtitle = document.createElement('p');
        var action = findPrimaryAction(content);
        var actionParent = action ? action.parentElement : null;
        var actionHeader = action ? action.closest('.card-header') : null;

        hero.className = 'v3-page-hero';
        copy.className = 'v3-page-hero__copy';
        icon.className = 'v3-page-hero__icon';
        icon.innerHTML = '<i class="' + getModuleIconClass() + '" aria-hidden="true"></i>';
        eyebrow.className = 'v3-page-hero__eyebrow';
        eyebrow.textContent = moduleLabel;
        title.removeAttribute('data-eyebrow');
        subtitle.textContent = description;

        text.appendChild(eyebrow);
        text.appendChild(title);
        text.appendChild(subtitle);
        copy.appendChild(icon);
        copy.appendChild(text);
        hero.appendChild(copy);

        if (action) {
            action.classList.add('v3-page-hero__action');
            hero.appendChild(action);
            removeEmptyParents(actionParent, actionHeader);
        }

        container.insertBefore(hero, container.firstChild);

        if (titleColumn && !titleColumn.textContent.trim() && !titleColumn.children.length) {
            titleColumn.remove();
        }
    }

    function enhancePrimaryCard(content) {
        var card = content.querySelector('.card');
        var header = card ? card.querySelector(':scope > .card-header') : null;
        var title = header ? header.querySelector('.card-title') : null;

        if (!header || !title || header.classList.contains('v3-section-heading')) {
            return;
        }

        var copy = document.createElement('div');
        var icon = document.createElement('span');
        var subtitle = document.createElement('p');

        header.classList.add('v3-section-heading');
        copy.className = 'v3-section-heading__copy';
        icon.className = 'v3-section-heading__icon';
        icon.innerHTML = '<i class="fas fa-list" aria-hidden="true"></i>';
        subtitle.textContent = i18n.section_description || '';

        header.insertBefore(icon, header.firstChild);
        header.insertBefore(copy, icon.nextSibling);
        copy.appendChild(title);
        copy.appendChild(subtitle);
    }

    var generatedId = 0;
    function nextId(prefix) {
        generatedId += 1;
        return (prefix || 'v3-control') + '-' + generatedId;
    }

    function cleanText(value) {
        return (value || '').replace(/\s+/g, ' ').trim();
    }

    function escapeSelector(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') { return window.CSS.escape(value); }
        return String(value).replace(/([ #;?%&,.+*~\':"!^$[\]()=>|\/@])/g, '\\$1');
    }

    function accessibleName(element) {
        var labelledBy = element.getAttribute('aria-labelledby');
        var labelled = labelledBy ? document.getElementById(labelledBy) : null;
        var label = element.id ? document.querySelector('label[for="' + escapeSelector(element.id) + '"]') : null;
        return cleanText(element.getAttribute('aria-label') || (labelled && labelled.textContent) || (label && label.textContent) || element.getAttribute('title'));
    }

    function inferActionLabel(element) {
        var icon = element.querySelector('i');
        var classes = icon ? icon.className : '';
        if (/trash|delete/.test(classes)) { return i18n.delete; }
        if (/edit|pencil/.test(classes)) { return i18n.edit; }
        if (/plus|add/.test(classes)) { return i18n.add; }
        if (/save|floppy/.test(classes)) { return i18n.save; }
        if (/times|close/.test(classes)) { return i18n.close; }
        if (/search/.test(classes)) { return i18n.search_system; }
        if (/angle-left|chevron-left/.test(classes)) { return i18n.previous; }
        if (/angle-right|chevron-right/.test(classes)) { return i18n.next; }
        if (/expand|arrows-alt/.test(classes)) { return i18n.expand; }
        if (/compress/.test(classes)) { return i18n.collapse; }
        return i18n.options || 'Action';
    }

    function enhanceForms(root) {
        root.querySelectorAll('input:not([type="hidden"]),select,textarea').forEach(function (field) {
            if (!field.id) { field.id = nextId('v3-field'); }
            var label = document.querySelector('label[for="' + escapeSelector(field.id) + '"]');
            if (!label) {
                var group = field.closest('.form-group,.input-group,.form-row,.row');
                var candidate = group ? group.querySelector('label') : null;
                if (candidate && !candidate.getAttribute('for')) {
                    candidate.setAttribute('for', field.id);
                    label = candidate;
                }
            }
            if (!accessibleName(field)) {
                var name = cleanText(field.getAttribute('placeholder') || field.getAttribute('name') || i18n.field);
                field.setAttribute('aria-label', name.replace(/[_-]+/g, ' '));
                field.setAttribute('data-v3-generated-label', 'true');
            }
            if (field.required) { field.setAttribute('aria-required', 'true'); }
            if (field.classList.contains('is-invalid')) { field.setAttribute('aria-invalid', 'true'); }
        });
    }

    function enhanceActions(root) {
        root.querySelectorAll('button,a[role="button"],a.btn').forEach(function (action) {
            if (!cleanText(action.textContent) && !accessibleName(action)) {
                action.setAttribute('aria-label', inferActionLabel(action));
                action.setAttribute('data-v3-generated-label', 'true');
            }
            action.querySelectorAll('i').forEach(function (icon) { icon.setAttribute('aria-hidden', 'true'); });
        });
    }

    function enhanceTables(root) {
        root.querySelectorAll('table').forEach(function (table) {
            if (!table.querySelector('caption')) {
                var caption = document.createElement('caption');
                var card = table.closest('.card');
                var title = card ? card.querySelector('.card-title,h2,h3') : null;
                caption.className = 'sr-only';
                caption.textContent = cleanText(title && title.textContent) || i18n.table || 'Data table';
                table.insertBefore(caption, table.firstChild);
            }
            table.querySelectorAll('thead th').forEach(function (header) { header.setAttribute('scope', 'col'); });
            var wrapper = table.parentElement && table.parentElement.classList.contains('table-responsive') ? table.parentElement : null;
            if (table.classList.contains('table') && table.parentElement && !wrapper) {
                wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
            if (wrapper) {
                wrapper.setAttribute('role', 'region');
                wrapper.setAttribute('tabindex', '0');
                wrapper.setAttribute('aria-label', cleanText(table.querySelector('caption').textContent));
            }
        });
    }

    function modalEntity(modal) {
        var explicit = cleanText(modal.getAttribute('data-modal-entity'));
        if (explicit) { return explicit; }
        var pageTitle = document.querySelector('.v3-page-hero h1, .content-header h1, .content > .container-fluid > .row .card-title');
        var entity = cleanText(pageTitle && pageTitle.textContent)
            .replace(/^listado de\s+/i, '')
            .replace(/^administraci[oó]n de\s+/i, '');
        var singular = {
            'Rubros': 'rubro', 'Usuarios': 'usuario', 'Especialidades': 'especialidad',
            'Ubicaciones': 'ubicación', 'Configuraciones': 'configuración',
            'Roles': 'rol', 'Bodegas': 'bodega', 'Empresas': 'empresa',
            'Transportistas': 'transportista', 'Empleados': 'empleado'
        };
        return singular[entity] || entity || 'registro';
    }

    function modalMode(modal) {
        var explicit = (modal.getAttribute('data-modal-mode') || '').toLowerCase();
        if (/edit|editar|modificar/.test(explicit)) { return 'edit'; }
        if (/new|nuevo|crear/.test(explicit)) { return 'new'; }
        var marker = modal.querySelector('[name="emily"], #emily, [name="modal_mode"], [data-modal-mode-value]');
        var markerValue = marker ? String(marker.value || marker.getAttribute('data-modal-mode-value') || '').toLowerCase() : '';
        if (/edit|editar|modificar/.test(markerValue)) { return 'edit'; }
        var idField = modal.querySelector('input[type="hidden"][name="id"], input[type="hidden"][name="edit_user"], input[type="hidden"][name="id_modelo"]');
        return idField && cleanText(idField.value) ? 'edit' : 'new';
    }

    function modalIconForTitle(text) {
        var value = cleanText(text).toLowerCase();
        var icons = [
            [/auditor[ií]a|bit[aá]cora|log\b/, 'fa-clipboard-check'],
            [/proveedor|acreedor/, 'fa-user-tie'],
            [/m[eé]dico|doctor|especialidad/, 'fa-user-md'],
            [/usuario|rol|permiso|seguridad/, 'fa-user-shield'],
            [/cliente|paciente/, 'fa-user-friends'],
            [/empleado|personal|beneficiario/, 'fa-id-card'],
            [/ubicaci[oó]n|direcci[oó]n|pa[ií]s|provincia|ciudad/, 'fa-map-marked-alt'],
            [/banco|bancaria|caja|conciliaci[oó]n/, 'fa-university'],
            [/transferencia|cruce de (cuentas|valores)/, 'fa-exchange-alt'],
            [/retenci[oó]n|tributari|impuesto/, 'fa-percentage'],
            [/compra|gasto/, 'fa-shopping-cart'],
            [/pedido|orden/, 'fa-clipboard-list'],
            [/factura|comprobante|nota de (cr[eé]dito|d[eé]bito)|egreso|ingreso/, 'fa-file-invoice-dollar'],
            [/documento|archivo|importar|carga/, 'fa-file-alt'],
            [/imagen|foto|logo/, 'fa-image'],
            [/producto|inventario|bodega|suministro/, 'fa-boxes'],
            [/activo|depreciaci[oó]n/, 'fa-industry'],
            [/transporte|transportista|gu[ií]a de remisi[oó]n/, 'fa-truck'],
            [/agenda|calendario|evento|cita/, 'fa-calendar-alt'],
            [/empresa|organizaci[oó]n|sucursal/, 'fa-building'],
            [/cuenta contable|plan de cuentas|asiento|contabilidad/, 'fa-balance-scale'],
            [/rubro|cat[aá]logo/, 'fa-tags'],
            [/men[uú]|configuraci[oó]n|utilidad/, 'fa-cogs'],
            [/reporte|informe|historial/, 'fa-chart-bar'],
            [/buscar|seleccionar/, 'fa-search'],
            [/visualizador|detalle|informaci[oó]n/, 'fa-eye']
        ];
        for (var index = 0; index < icons.length; index += 1) {
            if (icons[index][0].test(value)) { return icons[index][1]; }
        }
        return '';
    }

    function ensureModalTitleIcon(modal, title) {
        if (!title) { return; }
        var text = cleanText(title.textContent);
        var contextualIcon = modalIconForTitle(text + ' ' + modalEntity(modal));
        var icon = title.querySelector('i');
        var legacyIcons = {
            'fa-bank': 'fa-university',
            'fa-files-o': 'fa-folder-open',
            'fa-address-card-o': 'fa-id-card',
            'fa-calendar-days': 'fa-calendar-alt'
        };

        if (!icon) {
            icon = document.createElement('i');
            icon.className = 'fas ' + (contextualIcon || 'fa-info-circle') + ' mr-2';
            icon.setAttribute('aria-hidden', 'true');
            title.insertBefore(icon, title.firstChild);
            title.insertBefore(document.createTextNode(' '), icon.nextSibling);
            return;
        }

        if (icon.classList.contains('fa-solid')) {
            icon.classList.remove('fa-solid');
            icon.classList.add('fas');
        }
        if (icon.classList.contains('fa-regular')) {
            icon.classList.remove('fa-regular');
            icon.classList.add('far');
        }
        if (icon.classList.contains('fa') && !icon.classList.contains('fas') && !icon.classList.contains('far')) {
            icon.classList.remove('fa');
            icon.classList.add('fas');
        }
        if (!icon.classList.contains('fas') && !icon.classList.contains('far')) { icon.classList.add('fas'); }
        Object.keys(legacyIcons).forEach(function (legacy) {
            if (icon.classList.contains(legacy)) {
                icon.classList.remove(legacy);
                icon.classList.add(legacyIcons[legacy]);
            }
        });
        var generic = icon.classList.contains('fa-edit') || icon.classList.contains('fa-plus-square') || icon.classList.contains('fa-plus-circle');
        if (contextualIcon && generic) {
            icon.classList.remove('fa-edit', 'fa-plus-square', 'fa-plus-circle');
            icon.classList.remove('far');
            icon.classList.add('fas');
            icon.classList.add(contextualIcon);
        }
        icon.setAttribute('aria-hidden', 'true');
    }

    function setModalTitle(modal, mode, entity, detail) {
        if (typeof modal === 'string') { modal = document.querySelector(modal); }
        if (!modal) { return; }
        var title = modal.querySelector('.modal-title');
        if (!title) { return; }
        var editing = mode === 'edit' || mode === 'editar';
        var label = editing ? 'Editar ' : 'Nuevo ';
        var suffix = cleanText(detail) ? ': ' + cleanText(detail) : '';
        var titleText = label + cleanText(entity || modalEntity(modal)) + suffix;
        var icon = modalIconForTitle(titleText) || (editing ? 'fa-edit' : 'fa-plus-circle');
        title.innerHTML = '<i class="fas ' + icon + '" aria-hidden="true"></i> ' + titleText;
        modal.setAttribute('data-modal-mode', editing ? 'edit' : 'new');
    }

    function normalizeModalTitle(modal) {
        var title = modal && modal.querySelector('.modal-title');
        if (!title) { return; }
        var text = cleanText(title.textContent);
        if (/^modificar\b/i.test(text)) {
            title.innerHTML = '<i class="fas fa-edit" aria-hidden="true"></i> ' + text.replace(/^modificar\b/i, 'Editar');
            modal.setAttribute('data-modal-mode', 'edit');
            ensureModalTitleIcon(modal, title);
            return;
        }
        var provisional = !text || /^(cargando(?:\.{3}|…)?|modal title|editar|crear\s*\/\s*editar(?:\s+.+)?)$/i.test(text);
        if (provisional) {
            setModalTitle(modal, modalMode(modal), modalEntity(modal));
            return;
        }
        ensureModalTitleIcon(modal, title);
    }

    function enhanceDialogsAndStatus(root) {
        root.querySelectorAll('.modal').forEach(function (modal) {
            modal.setAttribute('role', 'dialog');
            modal.setAttribute('aria-modal', 'true');
            var title = modal.querySelector('.modal-title');
            if (title) {
                normalizeModalTitle(modal);
                if (!title.id) { title.id = nextId('v3-dialog-title'); }
                modal.setAttribute('aria-labelledby', title.id);
            } else if (!modal.getAttribute('aria-label')) {
                modal.setAttribute('aria-label', i18n.dialog || 'Dialog');
            }
        });
        root.querySelectorAll('.alert').forEach(function (alert) {
            alert.setAttribute('role', /danger|warning/.test(alert.className) ? 'alert' : 'status');
        });
        root.querySelectorAll('.preloader,.loading,.spinner-border,.spinner-grow').forEach(function (loader) {
            loader.setAttribute('role', 'status');
            loader.setAttribute('aria-live', 'polite');
            if (!accessibleName(loader)) { loader.setAttribute('aria-label', i18n.loading || 'Loading'); }
        });
    }

    function enhanceImages(root) {
        root.querySelectorAll('img:not([alt])').forEach(function (image) {
            image.setAttribute('alt', image.getAttribute('title') || '');
        });
    }

    function enhanceNavigation() {
        document.querySelectorAll('.nav-sidebar .nav-item').forEach(function (item) {
            var link = item.querySelector(':scope > .nav-link');
            var tree = item.querySelector(':scope > .nav-treeview');
            if (!link || !tree) { return; }
            if (!tree.id) { tree.id = nextId('v3-menu-group'); }
            link.setAttribute('aria-controls', tree.id);
            link.setAttribute('aria-expanded', item.classList.contains('menu-open') ? 'true' : 'false');
        });

        var sidebar = document.querySelector('.v3-sidebar-nav');
        if (!sidebar || sidebar.getAttribute('data-v3-scroll-ready') === 'true') { return; }
        sidebar.setAttribute('data-v3-scroll-ready', 'true');
        sidebar.addEventListener('click', function (event) {
            var link = event.target.closest('.nav-link');
            var item = link ? link.closest('.nav-item') : null;
            if (!item || !item.querySelector(':scope > .nav-treeview')) { return; }
            window.setTimeout(function () {
                link.setAttribute('aria-expanded', item.classList.contains('menu-open') ? 'true' : 'false');
                item.scrollIntoView({block: 'nearest'});
                var active = item.querySelector('.nav-treeview .nav-link.active');
                if (active) { active.scrollIntoView({block: 'nearest'}); }
            }, 220);
        });
    }

    function enhanceAccessibleUi(root) {
        enhanceForms(root);
        enhanceActions(root);
        enhanceTables(root);
        enhanceDialogsAndStatus(root);
        enhanceImages(root);
        enhanceNavigation();
    }

    function enhanceV3Views() {
        var shell = document.getElementById('contenido-principal');

        if (!shell) {
            return;
        }

        var header = shell.querySelector(':scope > .content-header');
        var content = shell.querySelector(':scope > .content');
        var sidebarSearch = document.querySelector('[data-widget="sidebar-search"] input');

        if (header && content) {
            buildPageHero(shell, header, content);
            enhancePrimaryCard(content);
        }

        if (sidebarSearch) {
            sidebarSearch.placeholder = i18n.search_menu || '';
            sidebarSearch.setAttribute('aria-label', i18n.search_menu || 'Menu search');
        }

        shell.querySelectorAll('table.table').forEach(function (table) {
            table.classList.add('table-hover');

            if (!table.parentElement.classList.contains('table-responsive')) {
                var wrapper = document.createElement('div');
                wrapper.className = 'table-responsive';
                table.parentNode.insertBefore(wrapper, table);
                wrapper.appendChild(table);
            }
        });

        enhanceAccessibleUi(document);

    }

    enhanceV3Views();
    window.AitrolModalTitle = {
        set: function (modal, mode, entity, detail) { setModalTitle(modal, mode, entity, detail); },
        normalize: normalizeModalTitle
    };
    if (window.jQuery) {
        window.jQuery(document).on('show.bs.modal shown.bs.modal', '.modal', function () {
            normalizeModalTitle(this);
        });
    }
    var observerTimer;
    new MutationObserver(function (mutations) {
        var hasAddedNodes = mutations.some(function (mutation) { return mutation.addedNodes && mutation.addedNodes.length; });
        if (!hasAddedNodes) { return; }
        window.clearTimeout(observerTimer);
        observerTimer = window.setTimeout(function () { enhanceAccessibleUi(document); }, 80);
    }).observe(document.body, {childList: true, subtree: true});
}());
