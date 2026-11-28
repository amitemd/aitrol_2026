(() => {
    'use strict';

    const preparados = new WeakSet();
    const etiquetas = { cedula: 'Cédula', ruc: 'RUC', pasaporte: 'Pasaporte' };
    let consecutivo = 0;

    function preparar(contenedor) {
        if (preparados.has(contenedor)) return;
        preparados.add(contenedor);

        const campo = contenedor.querySelector('[data-identificacion-persona]');
        const lista = contenedor.querySelector('[data-persona-resultados]');
        const estado = contenedor.querySelector('[data-persona-estado]');
        const formulario = campo.form;
        const tipo = formulario.elements.namedItem('tipo_identificacion');
        lista.id = 'lista-personas-' + ++consecutivo;
        campo.setAttribute('aria-controls', lista.id);
        let solicitud;
        let espera;
        let opcionActiva = -1;
        let seleccionPendiente = false;
        let ciudadAplicada = false;
        const aplicados = new Map();
        const ubicacion = formulario.querySelector('[data-ubicacion]');

        function informar(mensaje = '') {
            estado.textContent = mensaje;
            estado.hidden = !mensaje;
        }

        function cerrar() {
            lista.replaceChildren();
            lista.hidden = true;
            opcionActiva = -1;
            campo.setAttribute('aria-expanded', 'false');
            campo.removeAttribute('aria-activedescendant');
        }

        async function consultar(datos) {
            solicitud?.abort();
            const controlador = new AbortController();
            solicitud = controlador;
            try {
                const respuesta = await fetch(contenedor.dataset.selectorPersona, {
                    method: 'POST',
                    headers: { Accept: 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': formulario.elements.namedItem('_token').value },
                    body: JSON.stringify(datos),
                    signal: controlador.signal,
                });
                if (controlador.signal.aborted || !contenedor.isConnected) return;
                if (respuesta.status === 404 && datos.exacta) return null;
                if (!respuesta.ok) throw new Error();
                const resultado = await respuesta.json();
                return controlador.signal.aborted || !contenedor.isConnected ? undefined : resultado.data;
            } catch {
                if (!controlador.signal.aborted && contenedor.isConnected) informar('No se pudo consultar personas. Puedes continuar ingresando los datos.');
            }
        }

        async function seleccionar(opcion) {
            cerrar();
            campo.value = opcion.identificacion;
            if (tipo) {
                tipo.value = opcion.tipo_identificacion;
                window.jQuery?.(tipo).trigger('change.select2');
            }
            seleccionPendiente = true;
            informar('Cargando datos de la persona…');
            const persona = await consultar({ q: opcion.identificacion, tipo: opcion.tipo_identificacion, exacta: true });
            seleccionPendiente = false;
            if (persona === undefined) return;
            if (persona === null) {
                informar('La identificación ya no está en personas. Puedes ingresar los datos.');
                return;
            }

            const { identificacion, id_ciudad, ...datos } = persona;
            delete datos.tipo_identificacion;
            const mismaIdentificacion = identificacion === campo.defaultValue;
            if (formulario.elements.namedItem('email_contacto')) {
                datos.email_contacto = datos.email;
                delete datos.email;
            }
            for (const [nombre, valor] of Object.entries(datos)) {
                const entrada = formulario.elements.namedItem(nombre);
                if (!entrada || entrada.type === 'hidden' || (valor == null && mismaIdentificacion)) continue;
                entrada.value = valor ?? '';
                aplicados.set(entrada, entrada.value);
            }
            const nombreAnterior = formulario.elements.namedItem('nombres')?.closest('details');
            if (nombreAnterior) nombreAnterior.open = !persona.primer_nombre && Boolean(persona.nombres);
            if (id_ciudad != null || !mismaIdentificacion) {
                ubicacion?.dispatchEvent(new CustomEvent('aitrol:persona-ciudad', { detail: id_ciudad }));
                ciudadAplicada = id_ciudad != null;
            }
            informar('Datos de la persona cargados. Puedes revisarlos y completarlos.');
        }

        function buscar() {
            clearTimeout(espera);
            solicitud?.abort();
            cerrar();
            informar();
            for (const [entrada, valor] of aplicados) {
                if (entrada.value === valor) entrada.value = '';
            }
            aplicados.clear();
            if (ciudadAplicada) {
                ubicacion?.dispatchEvent(new CustomEvent('aitrol:persona-ciudad', { detail: null }));
                ciudadAplicada = false;
            }
            const q = campo.value.trim();
            if (!/^[A-Za-z0-9][A-Za-z0-9-]{2,29}$/.test(q)) return;

            espera = setTimeout(async () => {
                const personas = await consultar({ q });
                if (!personas || campo.ownerDocument.activeElement !== campo) return;
                if (!personas.length) {
                    informar('Sin coincidencias en personas. Puedes conservar la identificación y completar los datos.');
                    return;
                }
                personas.forEach((persona, indice) => {
                    const boton = campo.ownerDocument.createElement('button');
                    boton.type = 'button';
                    boton.tabIndex = -1;
                    boton.id = lista.id + '-' + indice;
                    boton.className = 'list-group-item list-group-item-action';
                    boton.setAttribute('role', 'option');
                    boton.setAttribute('aria-selected', 'false');
                    boton.textContent = `${persona.identificacion} · ${persona.nombre} (${etiquetas[persona.tipo_identificacion]})`;
                    boton.addEventListener('pointerdown', (evento) => evento.preventDefault());
                    boton.addEventListener('click', () => seleccionar(persona));
                    lista.append(boton);
                });
                lista.hidden = false;
                campo.setAttribute('aria-expanded', 'true');
            }, 250);
        }

        campo.addEventListener('input', buscar);
        formulario.addEventListener('submit', (evento) => {
            if (!seleccionPendiente) return;
            evento.preventDefault();
            evento.stopPropagation();
        });
        campo.addEventListener('blur', () => {
            clearTimeout(espera);
            if (!lista.hidden) cerrar();
        });
        campo.addEventListener('keydown', (evento) => {
            if (evento.key === 'Escape' && !lista.hidden) {
                evento.preventDefault();
                evento.stopPropagation();
                cerrar();
                return;
            }
            const opciones = [...lista.children];
            if (!opciones.length) return;
            if (evento.key === 'Enter' && opcionActiva >= 0) {
                evento.preventDefault();
                opciones[opcionActiva].click();
            }
            if (!['ArrowDown', 'ArrowUp'].includes(evento.key)) return;
            evento.preventDefault();
            const paso = evento.key === 'ArrowDown' ? 1 : -1;
            opcionActiva = opcionActiva < 0 ? (paso === 1 ? 0 : opciones.length - 1) : (opcionActiva + paso + opciones.length) % opciones.length;
            opciones.forEach((opcion, indice) => {
                opcion.classList.toggle('active', indice === opcionActiva);
                opcion.setAttribute('aria-selected', String(indice === opcionActiva));
            });
            campo.setAttribute('aria-activedescendant', opciones[opcionActiva].id);
            opciones[opcionActiva].scrollIntoView({ block: 'nearest' });
        });
    }

    function prepararFormularios(raiz) {
        raiz.querySelectorAll('[data-selector-persona]').forEach(preparar);
    }

    prepararFormularios(document);
    document.addEventListener('aitrol:form-ready', ({ target }) => prepararFormularios(target));
})();
