(() => {
    'use strict';

    const formulario = document.querySelector('[data-plan-busqueda]');
    if (!formulario) return;

    const entrada = formulario.querySelector('input[type="search"]');
    const ramasAbiertas = new Set();
    let arbol;
    let filtroAnterior;
    let turnoBusqueda;

    function normalizar(texto) {
        return texto.normalize('NFD').replace(/\p{M}/gu, '').toLowerCase().trim().replace(/\s+/g, ' ');
    }

    function indexar() {
        const seccion = document.querySelector('[data-plan-cuentas]');
        arbol = {
            raices: seccion.querySelector('.plan-cuentas-raices'),
            total: seccion.querySelector('[data-plan-total]'),
            controles: seccion.querySelector('[data-plan-controles]'),
            sinCoincidencias: seccion.querySelector('[data-plan-sin-coincidencias]'),
            vacio: seccion.querySelector('[data-plan-vacio]'),
            cuentas: Array.from(seccion.querySelectorAll('[data-plan-cuenta]'), (elemento) => ({
                elemento,
                padre: elemento.parentElement.closest('[data-plan-cuenta]'),
                codigo: normalizar(elemento.querySelector('.plan-cuenta-codigo').textContent),
                nombre: normalizar(elemento.querySelector('[data-plan-nombre]').textContent),
                boton: elemento.firstElementChild.querySelector('[data-plan-expandir]'),
                subcuentas: elemento.querySelector(':scope > .plan-cuentas-ramas'),
            })).reverse(),
        };
        filtroAnterior = undefined;
    }

    function cambiarVisibilidad(elemento, visible) {
        if (elemento && elemento.hidden === visible) elemento.hidden = !visible;
    }

    function ajustarRama(boton, subcuentas, abrir) {
        cambiarVisibilidad(subcuentas, abrir);
        if (boton.getAttribute('aria-expanded') !== String(abrir)) {
            boton.setAttribute('aria-expanded', String(abrir));
        }
    }

    function filtrar() {
        const filtro = normalizar(entrada.value);
        if (filtro === filtroAnterior) return;
        filtroAnterior = filtro;

        const padresVisibles = new Set();
        let cantidad = 0;
        for (const cuenta of arbol.cuentas) {
            const coincide = cuenta.codigo.includes(filtro) || cuenta.nombre.includes(filtro);
            const tieneCoincidencias = padresVisibles.has(cuenta.elemento);
            const visible = coincide || tieneCoincidencias;
            if (coincide) cantidad++;
            if (filtro && visible && cuenta.padre) padresVisibles.add(cuenta.padre);
            cambiarVisibilidad(cuenta.elemento, visible);
            if (cuenta.boton) {
                const abrir = filtro ? tieneCoincidencias : ramasAbiertas.has(cuenta.subcuentas.id);
                ajustarRama(cuenta.boton, cuenta.subcuentas, abrir);
            }
        }

        const etiqueta = filtro ? 'coincidencia' : 'cuenta';
        const texto = cantidad + ' ' + etiqueta + (cantidad === 1 ? '' : 's');
        if (arbol.total.textContent !== texto) arbol.total.textContent = texto;
        cambiarVisibilidad(arbol.raices, cantidad > 0);
        cambiarVisibilidad(arbol.controles, !filtro || padresVisibles.size > 0);
        cambiarVisibilidad(arbol.sinCoincidencias, Boolean(filtro) && cantidad === 0);
        cambiarVisibilidad(arbol.vacio, !filtro && cantidad === 0);
    }

    function programarBusqueda() {
        cancelAnimationFrame(turnoBusqueda);
        turnoBusqueda = requestAnimationFrame(filtrar);
    }

    function mostrarSubcuentas(boton, abrir) {
        const subcuentas = document.getElementById(boton.getAttribute('aria-controls'));
        if (!subcuentas || !subcuentas.querySelector(':scope > :not([hidden])')) return;

        ajustarRama(boton, subcuentas, abrir);
        if (!filtroAnterior) {
            if (abrir) ramasAbiertas.add(subcuentas.id);
            else ramasAbiertas.delete(subcuentas.id);
        }
    }

    entrada.addEventListener('input', (event) => {
        if (!event.isComposing) programarBusqueda();
    });
    entrada.addEventListener('compositionend', programarBusqueda);
    formulario.addEventListener('submit', (event) => {
        event.preventDefault();
        filtrar();
    });

    document.addEventListener('aitrol:listado-actualizado', () => {
        indexar();
        filtrar();
    });

    document.addEventListener('dblclick', (event) => {
        if (event.target.closest('button, a, [data-acciones-menu]')) return;
        const boton = event.target.closest('[data-plan-rama]')?.querySelector('[data-plan-expandir]');
        if (boton) mostrarSubcuentas(boton, boton.getAttribute('aria-expanded') === 'false');
    });

    document.addEventListener('click', (event) => {
        const boton = event.target.closest('[data-plan-expandir]');
        if (boton) {
            mostrarSubcuentas(boton, boton.getAttribute('aria-expanded') === 'false');
            return;
        }

        const control = event.target.closest('[data-plan-mostrar]');
        if (control) {
            const abrir = control.dataset.planMostrar === 'true';
            for (const cuenta of arbol.cuentas) {
                if (cuenta.boton && !cuenta.elemento.hidden) mostrarSubcuentas(cuenta.boton, abrir);
            }
        }
    });

    indexar();
    filtrar();
})();

(() => {
    'use strict';

    function prepararCodigo(formulario) {
        const grupo = formulario.querySelector('[data-codigo-auxiliar-url]');
        if (!grupo) return;

        const codigo = formulario.querySelector('#co-codigo');
        const ayuda = formulario.querySelector('#co-codigo-ayuda');
        let consulta;

        grupo.onchange = async () => {
            consulta?.abort();
            codigo.value = '';
            if (!grupo.value) {
                ayuda.textContent = 'Selecciona el grupo para ver el primer código libre.';
                return;
            }

            const actual = new AbortController();
            consulta = actual;
            const url = new URL(grupo.dataset.codigoAuxiliarUrl);
            url.searchParams.set('id_padre', grupo.value);
            ayuda.textContent = 'Consultando el primer código libre…';
            try {
                const respuesta = await fetch(url, { headers: { Accept: 'application/json' }, signal: actual.signal });
                const datos = await respuesta.json();
                if (actual.signal.aborted || !grupo.isConnected) return;
                if (respuesta.ok) {
                    codigo.value = datos.codigo;
                    ayuda.textContent = 'El sistema asignará el primer código libre al guardar.';
                } else {
                    ayuda.textContent = datos.errors?.id_padre?.[0] ?? 'Revisa el grupo seleccionado.';
                }
            } catch {
                if (actual.signal.aborted || !grupo.isConnected) return;
                ayuda.textContent = 'El código se calculará al guardar.';
            }
        };
    }

    document.querySelectorAll('[data-ajax-form]').forEach(prepararCodigo);
    document.querySelector('[data-ajax-dialog]')?.addEventListener('aitrol:form-ready', (evento) => prepararCodigo(evento.target));
})();
