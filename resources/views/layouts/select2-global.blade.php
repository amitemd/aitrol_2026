<script>
    (function (window, $) {
        'use strict';

        function initializeSelect2(root) {
            if (!$ || !$.fn || !$.fn.select2) return;
            var scope = root || document;
            $(scope).find('select').addBack('select')
                .filter(':not(.select2-hidden-accessible):not([data-select2-off])')
                .each(function () {
                    var select = $(this);
                    var firstOption = select.find('option:first');
                    var placeholder = select.data('placeholder') ||
                        (firstOption.length && !firstOption.val() ? firstOption.text() : 'Seleccione...');
                    var options = {
                        width: '100%',
                        language: {
                            noResults: function () { return 'No se encontraron resultados'; },
                            searching: function () { return 'Buscando...'; }
                        }
                    };
                    if (select.prop('multiple')) {
                        options.closeOnSelect = false;
                        options.placeholder = placeholder;
                    } else if (placeholder) {
                        options.placeholder = placeholder;
                        options.allowClear = !!(firstOption.length && !firstOption.val());
                    }
                    select.select2(options);
                });
        }

        $(function () {
            initializeSelect2(document);
            $(document).on('shown.bs.modal', function (event) {
                initializeSelect2(event.target);
            });
        });

        window.initializeGlobalSelect2 = initializeSelect2;
    }(window, window.jQuery));
</script>
