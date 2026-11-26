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

        function actualizarTipo() {
            if (!tipo) return;
            for (const section of container.querySelectorAll('[data-ubicacion-campo]')) {
                const field = section.dataset.ubicacionCampo;
                section.hidden = field !== tipo.value && !(tipo.value === 'ciudad' && field === 'provincia');
            }
        }

        tipo?.addEventListener('change', actualizarTipo);
        window.jQuery?.(tipo).on('change.ubicacion', actualizarTipo);
        actualizarTipo();

        if (window.aitrolSelect2) {
            setupSearch();
            return;
        }

        function setupSearch() {
            const $ = window.jQuery;
            function configurar(select, nivel, padre) {
                if (!select) return;
                window.aitrolSelect2(select, {
                    placeholder: 'Seleccionar',
                    allowClear: true,
                    ajax: {
                        url: container.dataset.url,
                        dataType: 'json',
                        delay: 250,
                        data: (params) => ({
                            nivel,
                            q: params.term ?? '',
                            page: params.page ?? 1,
                            ...(padre ? { [padre]: nivel === 'ciudad' ? provincia.value : pais.value } : {}),
                        }),
                    },
                });
            }

            function restablecer(select, activo) {
                if (!select) return;
                select.replaceChildren(new Option('Seleccionar', ''));
                $(select).val('').prop('disabled', !activo).trigger('change.select2');
            }

            function preseleccionar(select, dato, activo) {
                if (!select) return;
                if (dato) select.append(new Option(dato.text, dato.id, true, true));
                $(select).prop('disabled', !activo).trigger('change.select2');
            }

            $(pais).prop('disabled', false);
            configurar(pais, 'pais');
            configurar(provincia, 'provincia', 'pais');
            configurar(ciudad, 'ciudad', 'provincia');
            restablecer(provincia, false);
            restablecer(ciudad, false);
            let cambioManual = false;
            $(pais).on('change.ubicacion', () => {
                cambioManual = true;
                restablecer(provincia, Boolean(pais.value));
                restablecer(ciudad, false);
            });
            $(provincia).on('change.ubicacion', () => {
                cambioManual = true;
                restablecer(ciudad, Boolean(provincia.value));
            });

            const url = new URL(container.dataset.url);
            url.searchParams.set('seleccion', '1');
            if (container.dataset.ciudad) url.searchParams.set('ciudad', container.dataset.ciudad);
            else if (pais.dataset.selected) url.searchParams.set('pais', pais.dataset.selected);
            else return;

            fetch(url, { headers: { Accept: 'application/json' } })
                .then((response) => {
                    if (!response.ok) throw new Error();
                    return response.json();
                })
                .then((datos) => {
                    if (cambioManual || !container.isConnected) return;
                    preseleccionar(pais, datos.pais, true);
                    preseleccionar(provincia, datos.provincia, Boolean(datos.pais));
                    preseleccionar(ciudad, datos.ciudad, Boolean(datos.provincia));
                })
                .catch(() => {
                    if (!cambioManual) estado.textContent = 'No se pudo recuperar la ubicación seleccionada.';
                });
        }

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

        const initial = container.dataset.ciudad;
        load(initial ? { ciudad: initial } : { pais: pais.dataset.selected }, initial);
    }

    function setupAll(root) {
        root.querySelectorAll('[data-ubicacion]').forEach(setup);
    }

    setupAll(document);
    document.addEventListener('aitrol:form-ready', (event) => setupAll(event.target));
})();
