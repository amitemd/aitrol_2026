(function ($, window) {
    'use strict';

    var config = window.overtimeStatisticsConfig || {};
    var translations = config.translations || {};
    var table = null;

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
    }

    function filters() {
        return {
            id_sucursal: $('#overtime-branch').val() || '',
            nombre_empleado: $.trim($('#overtime-employee').val() || ''),
            anio_inicio: Number($('#overtime-year-from').val()),
            mes_inicio: Number($('#overtime-month-from').val()),
            anio_fin: Number($('#overtime-year-to').val()),
            mes_fin: Number($('#overtime-month-to').val())
        };
    }

    function hasValidRange(values) {
        var start = values.anio_inicio * 100 + values.mes_inicio;
        var end = values.anio_fin * 100 + values.mes_fin;
        return values.anio_inicio >= 2000 && values.anio_inicio <= 2100 &&
            values.anio_fin >= 2000 && values.anio_fin <= 2100 &&
            values.mes_inicio >= 1 && values.mes_inicio <= 12 &&
            values.mes_fin >= 1 && values.mes_fin <= 12 && start <= end;
    }

    function showAlert(message) {
        if (window.Swal && window.Swal.fire) {
            window.Swal.fire({
                icon: 'warning',
                text: message,
                confirmButtonColor: '#0e7490'
            });
        }
    }

    function formatCurrency(value) {
        var amount = Number(value || 0);
        if (window.Intl && window.Intl.NumberFormat) {
            return new window.Intl.NumberFormat(config.locale || 'es-EC', {
                style: 'currency',
                currency: config.currency || 'USD',
                minimumFractionDigits: 2
            }).format(amount);
        }
        return '$' + amount.toFixed(2);
    }

    function formatHours(value) {
        var hours = Number(value || 0);
        if (window.Intl && window.Intl.NumberFormat) {
            return new window.Intl.NumberFormat(config.locale || 'es-EC', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 2
            }).format(hours);
        }
        return hours.toFixed(2);
    }

    function formatPeriod(row) {
        var month = config.months && config.months[row.mes] ? config.months[row.mes] : row.mes;
        return '<span class="overtime-period"><i class="far fa-calendar-alt"></i>' +
            escapeHtml(month + ' ' + row.anio) + '</span>';
    }

    function showTableError() {
        $('#overtime-table-error').addClass('is-visible');
    }

    function hideTableError() {
        $('#overtime-table-error').removeClass('is-visible');
    }

    function initializeSelects() {
        if (!$.fn.select2) {
            return;
        }

        $('#overtime-branch').select2({
            width: '100%',
            minimumResultsForSearch: 8
        });
        $('#overtime-month-from, #overtime-month-to').select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });
    }

    function initializeTable() {
        table = $('#overtime-statistics-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            lengthChange: true,
            pageLength: 25,
            autoWidth: false,
            order: [[2, 'desc']],
            ajax: {
                url: config.ajaxUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken
                },
                data: function (request) {
                    $.extend(request, filters());
                },
                error: function () {
                    showTableError();
                }
            },
            columns: [
                {data: 'sucursal', render: function (data) {
                    return '<span class="overtime-branch"><i class="fas fa-map-marker-alt"></i>' + escapeHtml(data || translations.notAvailable) + '</span>';
                }},
                {data: 'empleado', render: function (data) {
                    return '<span class="overtime-employee">' + escapeHtml(data || translations.notAvailable) + '</span>';
                }},
                {data: null, render: function (data, type, row) { return formatPeriod(row); }},
                {data: 'sueldo_mensual', className: 'overtime-money overtime-salary', render: function (data) { return formatCurrency(data); }},
                {data: 'cantidad_horas50', className: 'overtime-hours', render: function (data) { return formatHours(data); }},
                {data: 'cantidad_horas100', className: 'overtime-hours', render: function (data) { return formatHours(data); }},
                {data: 'sobre_tiempo50', className: 'overtime-money', render: function (data) { return formatCurrency(data); }},
                {data: 'sobre_tiempo100', className: 'overtime-money', render: function (data) { return formatCurrency(data); }}
            ],
            language: {
                processing: translations.processing,
                emptyTable: translations.emptyTable,
                zeroRecords: translations.emptyTable,
                info: translations.info,
                infoEmpty: translations.infoEmpty,
                lengthMenu: translations.lengthMenu,
                paginate: {
                    first: translations.first,
                    last: translations.last,
                    next: translations.next,
                    previous: translations.previous
                }
            },
            drawCallback: function () {
                hideTableError();
            }
        });
    }

    function initializeEvents() {
        $('#overtime-filters').on('submit', function (event) {
            event.preventDefault();
            if (!hasValidRange(filters())) {
                showAlert(translations.invalidRange);
                return;
            }
            hideTableError();
            table.ajax.reload(null, true);
        });

        $('#overtime-clear').on('click', function () {
            var defaults = config.defaults || {};
            $('#overtime-branch').val('').trigger('change');
            $('#overtime-employee').val('');
            $('#overtime-year-from').val(defaults.yearFrom);
            $('#overtime-year-to').val(defaults.yearTo);
            $('#overtime-month-from').val(defaults.monthFrom).trigger('change');
            $('#overtime-month-to').val(defaults.monthTo).trigger('change');
            hideTableError();
            table.ajax.reload(null, true);
        });

        $('#overtime-export').on('click', function () {
            var values = filters();
            if (!hasValidRange(values)) {
                showAlert(translations.invalidRange);
                return;
            }
            window.location.assign(config.excelUrl + '?' + $.param(values));
        });
    }

    $(function () {
        if (!config.ajaxUrl || !$.fn.DataTable) {
            showTableError();
            return;
        }

        initializeSelects();
        initializeTable();
        initializeEvents();
    });
})(jQuery, window);
