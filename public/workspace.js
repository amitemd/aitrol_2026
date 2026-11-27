(() => {
    'use strict';

    const idioma = document.querySelector('[data-idioma-inmediato]');
    idioma?.addEventListener('change', () => idioma.form.requestSubmit());

    const fotosCuenta = document.querySelectorAll('[data-aitrol-avatar]');
    for (const foto of fotosCuenta) {
        const mostrarFoto = () => {
            foto.hidden = !foto.complete || foto.naturalWidth === 0;
            foto.previousElementSibling.hidden = !foto.hidden;
        };
        foto.addEventListener('load', mostrarFoto);
        foto.addEventListener('error', mostrarFoto);
        mostrarFoto();
    }

    function actualizarAvatar(url) {
        for (const foto of fotosCuenta) {
            foto.src = url;
        }
    }

    const recibirAvatar = (evento) => actualizarAvatar(evento.detail);
    window.addEventListener('aitrol:avatar-actualizado', recibirAvatar);

    const workspace = document.querySelector('[data-workspace]');
    if (document.documentElement.hasAttribute('data-workspace-embedded')) {
        document.addEventListener('click', (event) => {
            const link = event.target.closest('a[data-workspace-forward][data-workspace-link]');
            if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) return;
            const url = new URL(link.href);
            if (url.origin !== location.origin) return;
            const opened = window.parent.dispatchEvent(new CustomEvent('aitrol:abrir-destino', {
                cancelable: true, detail: { key: link.dataset.workspaceLink, label: link.dataset.workspaceLabel, url },
            }));
            if (!opened) event.preventDefault();
        });
    }
    if (!workspace || document.documentElement.hasAttribute('data-workspace-embedded')) {
        return;
    }

    const bar = document.querySelector('[data-workspace-tabs]');
    const navigation = document.querySelector('#navigation');
    if (!bar || !navigation) return;

    const content = workspace.querySelector('[data-workspace-content]');
    const initial = workspace.querySelector('[data-workspace-initial]');
    const dashboard = new URL(workspace.dataset.workspaceDashboard, location.href);
    const storageKey = 'aitrol.workspace';
    const session = workspace.dataset.workspaceSession;
    const tabs = new Map();
    let active;
    let panelNumber = 0;
    const menuTabs = document.createElement('div');
    menuTabs.className = 'workspace-context-menu';
    menuTabs.setAttribute('role', 'menu');
    menuTabs.setAttribute('aria-label', 'Opciones de pestañas');
    menuTabs.hidden = true;
    document.body.append(menuTabs);
    let origenMenu;

    function savedWorkspace() {
        if (!session) return null;
        try {
            const saved = JSON.parse(sessionStorage.getItem(storageKey));
            return saved?.session === session && Array.isArray(saved.keys) ? saved : null;
        } catch {
            return null;
        }
    }

    function saveWorkspace() {
        if (!session) return;
        try {
            sessionStorage.setItem(storageKey, JSON.stringify({ session, keys: [...tabs.keys()], active }));
        } catch {
            // Las pestañas siguen funcionando si el navegador bloquea el almacenamiento.
        }
    }

    function syncFrameTheme(frame) {
        const root = frame.contentDocument?.documentElement;
        if (!root?.hasAttribute('data-workspace-embedded')) return;
        const theme = document.documentElement.dataset.bsTheme;
        root.dataset.bsTheme = theme;
        root.style.colorScheme = theme;
    }

    new MutationObserver(() => {
        content.querySelectorAll('[data-workspace-frame]').forEach(syncFrameTheme);
    }).observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme'] });

    function updateSidebar() {
        navigation.querySelectorAll('.menu-open > .nav-link.active').forEach((toggle) => {
            toggle.classList.remove('active');
        });
        navigation.querySelectorAll('[data-workspace-link]').forEach((link) => {
            const selected = link.dataset.workspaceLink === active && tabs.get(active).url.pathname === new URL(link.href).pathname;
            link.classList.toggle('active', selected);
            if (selected) {
                link.setAttribute('aria-current', 'page');
                let group = link.closest('.nav-treeview');
                while (group) {
                    const parent = group.parentElement;
                    parent.classList.add('menu-open');
                    const toggle = parent.querySelector(':scope > .nav-link');
                    toggle?.classList.add('active');
                    toggle?.setAttribute('aria-expanded', 'true');
                    group = parent.closest('.nav-treeview');
                }
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }

    function activate(key, focus = false, registrar = false) {
        const selected = tabs.get(key);
        if (registrar && active !== key && selected.cargada && workspace.dataset.workspaceAudit) {
            fetch(workspace.dataset.workspaceAudit, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': workspace.dataset.workspaceCsrf,
                },
                body: JSON.stringify({ ruta: key }),
                keepalive: true,
            }).catch(() => {});
        }
        active = key;
        bar.hidden = tabs.size === 1;
        for (const [id, tab] of tabs) {
            tab.panel.hidden = id !== key;
            tab.button.setAttribute('aria-pressed', String(id === key));
        }
        if (focus && !bar.hidden) {
            selected.button.focus({ preventScroll: true });
            selected.item.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        }
        updateSidebar();
        saveWorkspace();
    }

    function close(key) {
        cerrarPestanas([key]);
    }

    function colapsarMenuLateral() {
        const buscador = navigation.closest('.app-sidebar')?.querySelector('[data-lte-toggle="sidebar-search"]');
        if (buscador?.value) {
            buscador.value = '';
            buscador.dispatchEvent(new Event('input', { bubbles: true }));
        }

        const grupos = [...navigation.querySelectorAll('.nav-item.menu-open')].reverse();
        for (const grupo of grupos) {
            globalThis.adminlte.Treeview.getOrCreateInstance(grupo).close();
            grupo.querySelector(':scope > .nav-link')?.classList.remove('active');
        }
    }

    function cerrarPestanas(seleccionadas) {
        const cerrar = new Set(seleccionadas.filter((key) => key !== 'dashboard' && tabs.has(key)));
        if (cerrar.size === 0) return;

        const keys = [...tabs.keys()];
        const next = cerrar.has(active)
            ? keys.slice(0, keys.indexOf(active)).reverse().find((key) => !cerrar.has(key)) ?? 'dashboard'
            : active;
        for (const key of cerrar) {
            const tab = tabs.get(key);
            tab.item.remove();
            tab.panel.remove();
            tabs.delete(key);
            if (tab.panel === initial) history.replaceState(history.state, '', dashboard);
        }
        activate(next, true, true);
        if (tabs.size === 1) colapsarMenuLateral();
    }

    function ocultarMenuTabs(devolverFoco = false) {
        if (menuTabs.hidden) return;
        menuTabs.hidden = true;
        if (devolverFoco && origenMenu?.isConnected) origenMenu.focus({ preventScroll: true });
    }

    function abrirMenuTabs(key, x, y) {
        const keys = [...tabs.keys()];
        const posicion = keys.indexOf(key);
        const opciones = [
            ['Cerrar esta pestaña', 'bi-x-lg', [key]],
            ['Cerrar otras pestañas', 'bi-window-stack', keys.filter((id) => id !== key)],
            ['Cerrar pestañas a la derecha', 'bi-arrow-right', keys.slice(posicion + 1)],
            ['Cerrar pestañas a la izquierda', 'bi-arrow-left', keys.slice(0, posicion)],
            ['Cerrar todas las pestañas', 'bi-x-square', keys],
        ];

        origenMenu = tabs.get(key).button;
        menuTabs.replaceChildren();
        const titulo = document.createElement('div');
        titulo.className = 'workspace-context-title';
        titulo.textContent = origenMenu.textContent;
        menuTabs.append(titulo);
        for (const [etiqueta, icono, ids] of opciones) {
            const boton = document.createElement('button');
            boton.type = 'button';
            boton.tabIndex = -1;
            boton.setAttribute('role', 'menuitem');
            boton.disabled = !ids.some((id) => id !== 'dashboard');
            const simbolo = document.createElement('i');
            simbolo.className = 'bi ' + icono;
            simbolo.setAttribute('aria-hidden', 'true');
            boton.append(simbolo, etiqueta);
            boton.addEventListener('click', () => {
                ocultarMenuTabs();
                cerrarPestanas(ids);
            });
            menuTabs.append(boton);
        }
        menuTabs.hidden = false;
        menuTabs.style.left = Math.max(8, Math.min(x, document.documentElement.clientWidth - menuTabs.offsetWidth - 8)) + 'px';
        menuTabs.style.top = Math.max(8, Math.min(y, window.innerHeight - menuTabs.offsetHeight - 8)) + 'px';
        menuTabs.querySelector('button:enabled')?.focus({ preventScroll: true });
    }

    menuTabs.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            event.preventDefault();
            ocultarMenuTabs(true);
            return;
        }
        if (event.key === 'Tab') {
            ocultarMenuTabs(true);
            return;
        }
        if (!['ArrowDown', 'ArrowUp', 'Home', 'End'].includes(event.key)) return;
        const botones = [...menuTabs.querySelectorAll('button:enabled')];
        const actual = botones.indexOf(document.activeElement);
        const paso = event.key === 'ArrowUp' ? -1 : 1;
        const siguiente = event.key === 'Home' ? 0 : event.key === 'End' ? botones.length - 1
            : (actual + paso + botones.length) % botones.length;
        event.preventDefault();
        botones[siguiente]?.focus();
    });
    document.addEventListener('pointerdown', (event) => {
        if (!menuTabs.contains(event.target)) ocultarMenuTabs();
    });
    bar.addEventListener('scroll', () => ocultarMenuTabs());
    window.addEventListener('resize', () => ocultarMenuTabs());
    window.addEventListener('blur', () => ocultarMenuTabs());

    async function refreshSidebar() {
        try {
            const result = await fetch(dashboard, { headers: { Accept: 'text/html' } });
            if (!result.ok) return;
            const page = new DOMParser().parseFromString(await result.text(), 'text/html');
            const updated = page.querySelector('#navigation');
            if (updated) {
                navigation.replaceChildren(...updated.childNodes);
                updateSidebar();
            }
        } catch {
            // La navegación actual sigue disponible si falla la solicitud.
        }
    }

    window.addEventListener('aitrol:menu-changed', refreshSidebar);

    function add(key, label, url, panel = null) {
        if (tabs.has(key)) {
            if (key === 'ventas.documentos.create') {
                const tab = tabs.get(key);
                const frame = tab.panel.querySelector('[data-workspace-frame]');
                if (tab.cargada && frame?.contentDocument?.location.pathname !== url.pathname && frame) {
                    tab.cargada = false;
                    frame.src = url.href;
                }
            }
            return;
        }

        if (!panel) {
            panel = document.createElement('div');
            const frame = document.createElement('iframe');
            frame.className = 'workspace-frame';
            frame.title = label;
            frame.setAttribute('data-workspace-frame', '');
            frame.addEventListener('load', () => {
                syncFrameTheme(frame);
                const tab = tabs.get(key);
                if (tab) tab.cargada = true;
                frame.contentWindow?.addEventListener('aitrol:menu-changed', refreshSidebar);
                frame.contentWindow?.addEventListener('aitrol:avatar-actualizado', recibirAvatar);
                const avatarGuardado = frame.contentDocument?.querySelector('[data-avatar-guardado]')?.dataset.avatarGuardado;
                if (avatarGuardado) actualizarAvatar(avatarGuardado);
                if (frame.contentDocument?.querySelector('[data-login-form], [data-empresa-inicial]')) {
                    location.assign(dashboard);
                } else if (frame.contentDocument?.querySelector('[data-workspace-menu-changed]')) {
                    refreshSidebar();
                }
            });
            frame.src = url.href;
            panel.append(frame);
            content.append(panel);
        }
        panel.id = 'workspace-panel-' + (++panelNumber);

        const item = document.createElement('span');
        item.className = 'workspace-tab';
        const button = document.createElement('button');
        button.type = 'button';
        button.className = 'btn btn-sm px-2';
        button.textContent = label;
        button.setAttribute('aria-controls', panel.id);
        button.addEventListener('click', () => activate(key, false, true));
        item.append(button);
        item.addEventListener('contextmenu', (event) => {
            event.preventDefault();
            const posicion = button.getBoundingClientRect();
            abrirMenuTabs(key, event.clientX || posicion.left, event.clientY || posicion.bottom);
        });
        item.addEventListener('keydown', (event) => {
            if (event.key !== 'ContextMenu' && !(event.shiftKey && event.key === 'F10')) return;
            event.preventDefault();
            const posicion = button.getBoundingClientRect();
            abrirMenuTabs(key, posicion.left, posicion.bottom + 4);
        });

        if (key !== 'dashboard') {
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'btn btn-sm px-2 text-body-secondary';
            remove.textContent = '×';
            remove.setAttribute('aria-label', 'Cerrar ' + label);
            remove.addEventListener('click', () => close(key));
            item.append(remove);
        }

        bar.append(item);
        tabs.set(key, { item, button, panel, url, cargada: panel === initial });
    }

    const initialKey = workspace.dataset.workspaceKey || 'dashboard';
    const initialLabel = document.title.split(' · Aitrol')[0].trim();
    const saved = savedWorkspace();
    const available = new Map([...navigation.querySelectorAll('a[data-workspace-link]')]
        .map((link) => [link.dataset.workspaceLink, link]));

    add('dashboard', 'Panel general', dashboard, initialKey === 'dashboard' ? initial : null);
    for (const key of saved?.keys ?? []) {
        if (key === 'dashboard') continue;
        if (key === initialKey) {
            add(key, initialLabel, new URL(location.href), initial);
            continue;
        }
        const link = available.get(key);
        if (!link) continue;
        const url = new URL(link.href, location.href);
        if (url.origin === location.origin && link.target !== '_blank') {
            add(key, link.dataset.workspaceLabel || link.textContent.trim(), url);
        }
    }
    if (initialKey !== 'dashboard') {
        add(initialKey, initialLabel, new URL(location.href), initial);
    }
    const reloaded = performance.getEntriesByType('navigation')[0]?.type === 'reload';
    activate((initialKey === 'dashboard' || reloaded) && tabs.has(saved?.active) ? saved.active : initialKey);

    window.addEventListener('aitrol:abrir-destino', (event) => {
        const { key, label, url, form } = event.detail;
        if (url.origin !== location.origin || (form && form.origin !== location.origin)) return;

        event.preventDefault();
        const menuLink = [...navigation.querySelectorAll('a[data-workspace-link]')]
            .find((link) => link.dataset.workspaceLink === key);
        add(key, menuLink?.dataset.workspaceLabel || menuLink?.textContent.trim() || label, url);
        activate(key, true, true);
        if (!form) return;

        const tab = tabs.get(key);
        const frame = tab.panel.querySelector('iframe[data-workspace-frame]');
        const abrirFormulario = (documento) => {
            [...(documento?.querySelectorAll('a[data-ajax-open]') ?? [])]
                .find((link) => link.href === form.href)?.click();
        };
        if (!frame) {
            abrirFormulario(document);
            return;
        }

        const pantallaActual = tab.cargada ? frame.contentDocument?.location.pathname : null;
        if (pantallaActual === url.pathname) {
            abrirFormulario(frame.contentDocument);
            return;
        }
        if (tab.formularioPendiente) frame.removeEventListener('load', tab.formularioPendiente);
        tab.formularioPendiente = () => {
            tab.formularioPendiente = null;
            if (active === key) abrirFormulario(frame.contentDocument);
        };
        frame.addEventListener('load', tab.formularioPendiente, { once: true });
        if (tab.cargada) {
            tab.cargada = false;
            frame.src = url.href;
        }
    });

    const search = document.querySelector('[data-workspace-search]');
    if (search) {
        const input = search.querySelector('input');
        const results = search.querySelector('#workspace-search-results');
        const links = search.querySelector('[data-search-links]');
        const empty = search.querySelector('[data-search-empty]');
        const status = search.querySelector('[data-search-status]');
        const normalize = (text) => text.normalize('NFD').replace(/\p{Diacritic}/gu, '').toLowerCase().trim();

        function renderSearch() {
            const term = normalize(input.value);
            results.hidden = !term;
            links.replaceChildren();
            status.textContent = '';
            if (!term) return;

            const matches = [...navigation.querySelectorAll('a[data-workspace-link]')]
                .filter((link) => normalize(link.dataset.workspaceLabel || link.textContent).includes(term))
                .slice(0, 8);

            for (const option of matches) {
                const row = document.createElement('li');
                const link = document.createElement('a');
                link.href = option.href;
                link.dataset.workspaceLink = option.dataset.workspaceLink;
                link.textContent = option.dataset.workspaceLabel || option.textContent.trim();
                row.append(link);
                links.append(row);
            }
            empty.hidden = matches.length > 0;
            status.textContent = matches.length === 1 ? '1 resultado.' : `${matches.length} resultados.`;
        }

        input.addEventListener('input', renderSearch);
        input.addEventListener('focus', renderSearch);
        search.addEventListener('focusout', (event) => {
            if (!search.contains(event.relatedTarget)) results.hidden = true;
        });
        search.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !results.hidden) {
                event.preventDefault();
                input.focus();
                results.hidden = true;
                status.textContent = '';
            } else if (event.key === 'ArrowDown' && event.target === input && !results.hidden) {
                const first = links.querySelector('a');
                if (first) {
                    event.preventDefault();
                    first.focus();
                }
            }
        });
        search.addEventListener('submit', (event) => {
            event.preventDefault();
            renderSearch();
            links.querySelector('a')?.click();
        });
        search.hidden = false;
    }

    document.addEventListener('click', (event) => {
        const link = event.target.closest('a[data-workspace-link]');
        if (!link || event.defaultPrevented || event.button !== 0 || event.ctrlKey || event.metaKey || event.shiftKey || event.altKey) {
            return;
        }

        const url = new URL(link.href, location.href);
        if (url.origin !== location.origin || link.target === '_blank') {
            return;
        }

        event.preventDefault();
        const key = link.dataset.workspaceLink;
        add(key, link.dataset.workspaceLabel || link.textContent.trim(), url);
        if (search?.contains(link)) {
            search.reset();
            search.querySelector('#workspace-search-results').hidden = true;
            search.querySelector('[data-search-status]').textContent = '';
            if (tabs.size === 1) search.querySelector('input').focus();
        }
        activate(key, true, true);
    });
})();
