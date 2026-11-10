(function (window, document) {
    'use strict';

    function initializeCatalogs() {
        var $ = window.jQuery;

        if (! $ || ! $.fn || ! $.fn.select2 || ! window.aitrolClinicalCatalogUrl) {
            return;
        }

        $('.js-clinical-catalog').each(function () {
            var $select = $(this);

            if ($select.hasClass('select2-hidden-accessible')) {
                return;
            }

            $select.select2({
                allowClear: true,
                ajax: {
                    cache: true,
                    data: function (params) {
                        return {
                            page: params.page || 1,
                            q: params.term || '',
                            tipo: $select.data('catalog')
                        };
                    },
                    dataType: 'json',
                    delay: 250,
                    processResults: function (data) {
                        return {
                            pagination: data.pagination || { more: false },
                            results: data.results || []
                        };
                    },
                    url: window.aitrolClinicalCatalogUrl
                },
                language: {
                    errorLoading: function () { return 'No se pudo cargar la búsqueda'; },
                    noResults: function () { return 'No se encontraron resultados'; },
                    searching: function () { return 'Buscando…'; }
                },
                minimumInputLength: 0,
                placeholder: $select.data('placeholder'),
                width: '100%'
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCatalogs);
    } else {
        initializeCatalogs();
    }
}(window, document));
