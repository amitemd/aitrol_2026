(() => {
    'use strict';

    const $ = window.jQuery;
    if (!$?.fn.select2) return;

    const geografico = '[data-ubicacion-pais], [data-ubicacion-provincia], [data-ubicacion-ciudad]';

    function inicializar(select, configuracion = {}) {
        const campo = $(select);
        if (campo.data('select2')) return campo;
        const contenedor = select.closest('dialog, [data-select2-contenedor]');
        campo.select2({
            width: '100%',
            language: {
                noResults: () => 'No se encontraron resultados',
                searching: () => 'Buscando…',
                loadingMore: () => 'Cargando más resultados…',
                errorLoading: () => 'No se pudieron cargar los resultados',
                inputTooShort: ({ minimum, input }) => 'Escribe ' + (minimum - input.length) + ' caracteres más',
            },
            ...(contenedor ? { dropdownParent: $(contenedor) } : {}),
            ...configuracion,
        });

        return campo;
    }

    function preparar(select) {
        if (select.matches(geografico + ', [data-select2-nativo]') || select.closest('.swal2-container')) return;
        const url = select.dataset.select2Url;
        const retencion = select.hasAttribute('data-retencion-catalogo');
        const datosRetencion = () => ({
            tipo: select.closest('[data-comercial-linea]').querySelector('[data-retencion-tipo]').value,
            fecha: select.form.elements.fecha.value,
        });
        inicializar(select, url ? {
            ...(select.dataset.select2Placeholder ? { placeholder: select.dataset.select2Placeholder, allowClear: true } : {}),
            minimumInputLength: Number(select.dataset.select2Min || 1),
            ajax: {
                url,
                dataType: 'json',
                delay: 250,
                data: (params) => ({ q: params.term ?? '', page: params.page ?? 1, ...(retencion ? datosRetencion() : {}) }),
            },
            ...(retencion ? {
                dropdownAutoWidth: true,
                tags: true,
                createTag: ({ term }) => {
                    const recibida = select.hasAttribute('data-retencion-recibida');
                    const codigo = recibida ? term.trim() : term.trim().toUpperCase();
                    const valido = recibida
                        ? codigo.length > 0 && codigo.length <= 32
                        : /^[0-9][0-9A-Z]{0,4}$/.test(codigo);
                    return valido
                        ? { id: codigo, text: codigo + ' · Ingresar código manualmente' } : null;
                },
                insertTag: (resultados, opcion) => {
                    const codigo = opcion.id.toUpperCase();
                    if (!resultados.some((resultado) => String(resultado.id).toUpperCase() === codigo)) resultados.push(opcion);
                },
            } : {}),
        } : {});
    }

    function tasaDe(select) {
        return select.closest('[data-comercial-linea]').querySelector('input[name$="[porcentaje_retencion]"]');
    }

    $(document).on('select2:open', '[data-retencion-catalogo]', function () {
        const contenedor = this.closest('dialog, [data-select2-contenedor]') ?? this.ownerDocument.body;
        const menu = contenedor.querySelector('.select2-container--open > .select2-dropdown');
        if (!menu) return;

        const espacio = this.ownerDocument.documentElement.clientWidth;
        const ancho = Math.min(480, espacio - 24);
        const izquierda = this.nextElementSibling.getBoundingClientRect().left;
        menu.classList.add('retencion-catalogo-desplegable');
        menu.style.setProperty('--retencion-menu-ancho', ancho + 'px');
        menu.style.setProperty('--retencion-menu-desplazamiento', Math.min(0, espacio - 12 - izquierda - ancho) + 'px');
    });

    function completarRetencion(select, opcion) {
        const tasa = tasaDe(select);
        tasa.value = opcion?.porcentaje ?? '';
        tasa.dispatchEvent(new Event('input', { bubbles: true }));
        const concepto = select.closest('[data-comercial-linea]').querySelector('input[name$="[descripcion]"]');
        if (concepto && (!concepto.value || concepto.value === concepto.dataset.descripcionCatalogo)) {
            concepto.value = concepto.dataset.descripcionCatalogo = opcion?.descripcion ?? '';
        }
    }

    $(document).on('select2:select select2:clear', '[data-retencion-catalogo]', function (evento) {
        completarRetencion(this, evento.params?.data);
    });
    function limpiarCodigo(select) {
        $(select).val('').trigger('change');
        completarRetencion(select);
    }
    $(document).on('change', '[data-retencion-tipo]', function () {
        limpiarCodigo(this.closest('[data-comercial-linea]').querySelector('[data-retencion-catalogo]'));
    });
    $(document).on('change', 'form [name="fecha"]', function () {
        this.form.querySelectorAll('[data-retencion-catalogo]').forEach(limpiarCodigo);
    });

    window.aitrolSelect2 = inicializar;
    document.querySelectorAll('select').forEach(preparar);
    function recorrer(node, accion) {
        if (!(node instanceof Element)) return;
        if (node.matches('select')) accion(node);
        else node.querySelectorAll('select').forEach(accion);
    }

    new MutationObserver((changes) => {
        for (const change of changes) {
            for (const node of change.addedNodes) {
                if (node.isConnected) recorrer(node, preparar);
            }
            for (const node of change.removedNodes) {
                if (!node.isConnected) recorrer(node, (select) => {
                    if ($(select).data('select2')) $(select).select2('destroy');
                });
            }
        }
    }).observe(document.body, { childList: true, subtree: true });
})();
