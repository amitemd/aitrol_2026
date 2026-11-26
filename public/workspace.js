(() => {
    'use strict';

    const workspace = document.querySelector('[data-workspace]');
    if (!workspace || document.documentElement.hasAttribute('data-workspace-embedded')) {
        return;
    }

    const bar = workspace.querySelector('[data-workspace-tabs]');
    const content = workspace.querySelector('[data-workspace-content]');
    const initial = workspace.querySelector('[data-workspace-initial]');
    const dashboard = new URL(workspace.dataset.workspaceDashboard, location.href);
    const tabs = new Map();
    let active;
    let panelNumber = 0;

    function updateSidebar() {
        const navigation = document.querySelector('#navigation');
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

    function activate(key, focus = false) {
        active = key;
        bar.hidden = tabs.size === 1;
        for (const [id, tab] of tabs) {
            tab.panel.hidden = id !== key;
            tab.button.setAttribute('aria-pressed', String(id === key));
        }
        const selected = tabs.get(key);
        if (focus && !bar.hidden) {
            selected.button.focus({ preventScroll: true });
            selected.item.scrollIntoView({ block: 'nearest', inline: 'nearest' });
        }
        updateSidebar();
    }

    function close(key) {
        const tab = tabs.get(key);
        const keys = [...tabs.keys()];
        const next = keys[keys.indexOf(key) - 1] || 'dashboard';
        tab.item.remove();
        tab.panel.remove();
        tabs.delete(key);
        activate(active === key ? next : active, true);
    }

    async function refreshSidebar() {
        try {
            const result = await fetch(dashboard, { headers: { Accept: 'text/html' } });
            if (!result.ok) return;
            const page = new DOMParser().parseFromString(await result.text(), 'text/html');
            const updated = page.querySelector('#navigation');
            if (updated) {
                document.querySelector('#navigation').replaceChildren(...updated.childNodes);
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
                frame.contentWindow?.addEventListener('aitrol:menu-changed', refreshSidebar);
                if (frame.contentDocument?.querySelector('[data-login-form]')) {
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
        button.addEventListener('click', () => activate(key));
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
        tabs.set(key, { item, button, panel, url });
    }

    const initialKey = workspace.dataset.workspaceKey || 'dashboard';
    add('dashboard', 'Panel general', dashboard, initialKey === 'dashboard' ? initial : null);
    if (initialKey !== 'dashboard') {
        add(initialKey, document.title.split(' · Aitrol')[0].trim(), new URL(location.href), initial);
    }
    activate(initialKey);

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
        activate(key, true);
    });
})();
