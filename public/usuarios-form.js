(() => {
    'use strict';

    function setup(form) {
        const tablist = form.querySelector('.usuario-tabs');
        const tabs = [...tablist.querySelectorAll('[data-usuario-tab]')];
        const panels = [...form.querySelectorAll('[data-usuario-panel]')];

        function select(name) {
            for (const tab of tabs) {
                const active = tab.dataset.usuarioTab === name;
                tab.classList.toggle('active', active);
                tab.setAttribute('aria-selected', String(active));
                tab.tabIndex = active ? 0 : -1;
            }
            for (const panel of panels) panel.hidden = panel.dataset.usuarioPanel !== name;
        }

        tablist.addEventListener('click', (event) => {
            const tab = event.target.closest('[data-usuario-tab]');
            if (tab) select(tab.dataset.usuarioTab);
        });
        tablist.addEventListener('keydown', (event) => {
            const index = tabs.indexOf(event.target);
            const destinations = {
                ArrowLeft: (index + tabs.length - 1) % tabs.length,
                ArrowRight: (index + 1) % tabs.length,
                Home: 0,
                End: tabs.length - 1,
            };
            if (index < 0 || !Object.hasOwn(destinations, event.key)) return;
            const next = tabs[destinations[event.key]];
            event.preventDefault();
            select(next.dataset.usuarioTab);
            next.focus();
        });

        form.addEventListener('aitrol:ficha-error', (event) => select(event.target.dataset.usuarioPanel));
        form.addEventListener('invalid', (event) => {
            const panel = event.target.closest('[data-usuario-panel]');
            if (panel?.hidden && !form.querySelector('[data-usuario-panel]:not([hidden]) :invalid')) {
                select(panel.dataset.usuarioPanel);
            }
        }, true);

        const invalid = form.querySelector('.is-invalid')?.closest('[data-usuario-panel]');
        select(invalid?.dataset.usuarioPanel ?? 'generales');
        tablist.hidden = false;
    }

    document.querySelectorAll('[data-usuario-form]').forEach(setup);
    document.addEventListener('aitrol:form-ready', (event) => {
        if (event.target.matches('[data-usuario-form]')) setup(event.target);
    });
})();
