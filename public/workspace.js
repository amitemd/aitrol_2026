(() => {
    'use strict';

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
        const tab = tabs.get(key);
        const keys = [...tabs.keys()];
        const next = keys[keys.indexOf(key) - 1] || 'dashboard';
        tab.item.remove();
        tab.panel.remove();
        tabs.delete(key);
        if (tab.panel === initial) history.replaceState(history.state, '', dashboard);
        activate(active === key ? next : active, true, true);
    }

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
