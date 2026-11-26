(() => {
    const initialized = new WeakSet();

    function setup(container) {
        if (initialized.has(container)) return;
        initialized.add(container);

        const pais = container.querySelector('[data-ubicacion-pais]');
        const provincia = container.querySelector('[data-ubicacion-provincia]');
        const ciudad = container.querySelector('[data-ubicacion-ciudad]');
        const tipo = container.querySelector('[data-ubicacion-tipo]');
        const estado = container.querySelector('[data-ubicacion-estado]');
        let pending;

        function options(select, rows, selected, enabled = true) {
            if (!select) return;
            const fragment = document.createDocumentFragment();
            fragment.append(new Option('Seleccionar', ''));
            for (const { id, nombre } of rows) fragment.append(new Option(nombre, id));
            select.replaceChildren(fragment);
            select.value = selected ?? '';
            select.disabled = !enabled || rows.length === 0;
        }

        async function load(params, selectedCity = '') {
            pending?.abort();
            const controller = new AbortController();
            pending = controller;
            estado.textContent = 'Cargando ubicaciones…';
            const url = new URL(container.dataset.url);
            for (const [key, value] of Object.entries(params)) {
                if (value) url.searchParams.set(key, value);
            }

            try {
                const response = await fetch(url, { headers: { Accept: 'application/json' }, signal: controller.signal });
                if (!response.ok) throw new Error();
                const data = await response.json();
                if (controller.signal.aborted) return;
                options(pais, data.paises, data.pais);
                options(provincia, data.provincias, data.provincia, Boolean(data.pais));
                options(ciudad, data.ciudades, selectedCity, Boolean(data.provincia));
                estado.textContent = '';
            } catch {
                if (!controller.signal.aborted) estado.textContent = 'No se pudieron cargar las ubicaciones. Vuelve a intentarlo.';
            }
        }

        pais.addEventListener('change', () => {
            options(provincia, [], '', false);
            options(ciudad, [], '', false);
            load({ pais: pais.value });
        });
        provincia.addEventListener('change', () => {
            options(ciudad, [], '', false);
            load({ pais: pais.value, provincia: provincia.value });
        });

        if (tipo) {
            const update = () => {
                for (const section of container.querySelectorAll('[data-ubicacion-campo]')) {
                    const field = section.dataset.ubicacionCampo;
                    section.hidden = field !== tipo.value && !(tipo.value === 'ciudad' && field === 'provincia');
                }
            };
            tipo.addEventListener('change', update);
            update();
        }

        const initial = container.dataset.ciudad;
        load(initial ? { ciudad: initial } : { pais: pais.dataset.selected }, initial);
    }

    function setupAll(root) {
        root.querySelectorAll('[data-ubicacion]').forEach(setup);
    }

    setupAll(document);
    document.addEventListener('aitrol:form-ready', (event) => setupAll(event.target));
})();
