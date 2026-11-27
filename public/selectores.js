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
        inicializar(select, url ? {
            ...(select.dataset.select2Placeholder ? { placeholder: select.dataset.select2Placeholder, allowClear: true } : {}),
            minimumInputLength: Number(select.dataset.select2Min || 1),
            ajax: {
                url,
                dataType: 'json',
                delay: 250,
                data: (params) => ({ q: params.term ?? '', page: params.page ?? 1 }),
            },
        } : {});
    }

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
