(function ($, window) {
    'use strict';

    var config = window.payrollEmployeesConfig || {};
    var translations = config.translations || {};
    var table = null;

    function renderEmptyState() {
        var cell = $('#payroll-employees-table tbody td.dataTables_empty');
        if (!cell.length) {
            return;
        }

        cell.addClass('payroll-datatable-empty-cell').html(
            '<div class="payroll-datatable-empty" role="status">' +
                '<i class="fas fa-inbox" aria-hidden="true"></i>' +
                '<strong>' + escapeHtml(translations.emptyTitle || translations.emptyTable) + '</strong>' +
                '<span>' + escapeHtml(translations.emptyHelp || '') + '</span>' +
            '</div>'
        );
    }

    function escapeHtml(value) {
        return $('<div>').text(value === null || value === undefined ? '' : value).html();
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

    function formatDate(value) {
        if (!value) {
            return translations.notAvailable;
        }

        var parts = String(value).substring(0, 10).split('-');
        if (parts.length !== 3) {
            return escapeHtml(value);
        }

        var date = new Date(Number(parts[0]), Number(parts[1]) - 1, Number(parts[2]));
        if (isNaN(date.getTime())) {
            return escapeHtml(value);
        }

        if (window.Intl && window.Intl.DateTimeFormat) {
            return new window.Intl.DateTimeFormat(config.locale || 'es-EC', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            }).format(date);
        }

        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function anniversaryMessage(value) {
        if (!value) {
            return '';
        }

        var parts = String(value).substring(0, 10).split('-');
        if (parts.length !== 3) {
            return '';
        }

        var anniversary = new Date(Number(parts[0]) + 1, Number(parts[1]) - 1, Number(parts[2]));
        var today = new Date();
        today.setHours(0, 0, 0, 0);
        anniversary.setHours(0, 0, 0, 0);

        var days = Math.ceil((anniversary.getTime() - today.getTime()) / 86400000);
        if (days === 0) {
            return '<span class="payroll-anniversary is-today"><i class="fas fa-birthday-cake"></i>' +
                escapeHtml(translations.firstAnniversary) + '</span>';
        }
        if (days > 0 && days <= 31) {
            return '<span class="payroll-anniversary"><i class="fas fa-hourglass-half"></i>' +
                escapeHtml(String(translations.daysToAnniversary || '').replace(':days', days)) + '</span>';
        }

        return '';
    }

    function scheduleLabel(value) {
        var labels = {
            0: translations.completeSchedule,
            1: translations.halfSchedule,
            2: translations.partialSchedule
        };
        var schedule = Object.prototype.hasOwnProperty.call(labels, value) ? labels[value] : translations.notAvailable;

        return '<span class="payroll-schedule"><i class="far fa-clock"></i>' + escapeHtml(schedule) + '</span>';
    }

    function balanceActions(row) {
        return '<div class="payroll-balance-actions">' +
            '<a class="payroll-icon-button" href="' + escapeHtml(row.urls.detalle_saldos) + '" target="_blank" rel="noopener" title="' + escapeHtml(translations.details) + '">' +
                '<i class="fas fa-eye"></i><span class="sr-only">' + escapeHtml(translations.details) + '</span>' +
            '</a>' +
            '<a class="payroll-icon-button is-excel" href="' + escapeHtml(row.urls.excel_saldos) + '" target="_blank" rel="noopener" title="' + escapeHtml(translations.excel) + '">' +
                '<i class="fas fa-file-excel"></i><span class="sr-only">' + escapeHtml(translations.excel) + '</span>' +
            '</a>' +
        '</div>';
    }

    function dropdownItem(url, icon, label, attributes, danger, extraClass) {
        return '<li><a class="payroll-dropdown-item' + (danger ? ' is-danger' : '') + (extraClass ? ' ' + extraClass : '') + '" href="' + escapeHtml(url || '#') + '"' + (attributes || '') + '>' +
            '<i class="' + escapeHtml(icon) + '"></i><span>' + escapeHtml(label) + '</span>' +
        '</a></li>';
    }

    function actionMenu(row) {
        var items = '';

        if (Number(row.estado) === 1) {
            items += dropdownItem(row.urls.visualizar, 'fas fa-eye', translations.view);
            items += dropdownItem(row.urls.inactivar, 'fas fa-user-slash', translations.deactivate, ' data-deactivate-url="' + escapeHtml(row.urls.inactivar) + '"', true, 'payroll-deactivate');
        }

        items += dropdownItem(row.urls.roles, 'fas fa-receipt', translations.newRoles);

        if (Number(row.estado) === 1) {
            items += dropdownItem(row.urls.detalle_saldos, 'fas fa-file-invoice-dollar', translations.loanBalanceReport);
            items += dropdownItem(row.urls.prestamos, 'fas fa-hand-holding-usd', translations.loans, ' data-toggle="modal" data-target="#modal_prestamos_empleado"');
            items += dropdownItem(row.urls.anticipos, 'fas fa-money-check-alt', translations.otherAdvances, ' data-toggle="modal" data-target="#modal_otros_anticipos_empleado"');
            items += dropdownItem(row.urls.saldos_iniciales, 'fas fa-coins', translations.initialBalances, ' data-toggle="modal" data-target="#modal_saldos_iniciales"');
        }

        return '<div class="dropdown payroll-actions">' +
            '<button type="button" class="btn payroll-actions-toggle dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                '<i class="fas fa-ellipsis-v"></i><span>' + escapeHtml(translations.options) + '</span><span class="caret"></span>' +
            '</button>' +
            '<ul class="dropdown-menu dropdown-menu-right">' + items + '</ul>' +
        '</div>';
    }

    function initializeSelects() {
        if (!$.fn.select2) {
            return;
        }

        $('#payroll-status').select2({
            width: '100%',
            minimumResultsForSearch: Infinity
        });
        $('#payroll-branch').select2({
            width: '100%',
            minimumResultsForSearch: 8
        });
    }

    function showTableError() {
        $('#payroll-table-error').addClass('is-visible');
    }

    function hideTableError() {
        $('#payroll-table-error').removeClass('is-visible');
    }

    function initializeTable() {
        table = $('#payroll-employees-table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            searching: false,
            lengthChange: true,
            pageLength: 25,
            autoWidth: false,
            order: [[1, 'asc']],
            ajax: {
                url: config.ajaxUrl,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': config.csrfToken
                },
                data: function (request) {
                    request.estado = $('#payroll-status').val();
                    request.identificacion = $('#payroll-identification').val();
                    request.empleado = $('#payroll-employee').val();
                    request.id_sucursal = $('#payroll-branch').val();
                },
                error: function () {
                    showTableError();
                }
            },
            columns: [
                {data: 'id_user', render: function (data) { return '<strong class="payroll-identification">' + escapeHtml(data) + '</strong>'; }},
                {data: 'empleado', render: function (data) { return '<span class="payroll-employee-name">' + escapeHtml(data || translations.notAvailable) + '</span>'; }},
                {data: 'cargo', render: function (data) { return escapeHtml(data || translations.notAvailable); }},
                {data: 'sueldo_neto', className: 'payroll-money', render: function (data) { return formatCurrency(data); }},
                {data: 'fecha_ingreso', render: function (data) { return '<span class="payroll-date">' + formatDate(data) + '</span>' + anniversaryMessage(data); }},
                {data: 'estado', className: 'text-center', render: function (data) {
                    var active = Number(data) === 1;
                    return '<span class="payroll-status ' + (active ? 'is-active' : 'is-inactive') + '"><i class="fas ' + (active ? 'fa-check-circle' : 'fa-minus-circle') + '"></i>' +
                        escapeHtml(active ? translations.active : translations.inactive) + '</span>';
                }},
                {data: null, className: 'text-center', orderable: false, searchable: false, render: function (data, type, row) { return balanceActions(row); }},
                {data: 'saldo_prestamos', className: 'payroll-money', render: function (data) { return formatCurrency(data); }},
                {data: 'saldo_inicial', className: 'payroll-money', render: function (data) { return formatCurrency(data); }},
                {data: 'jornada', render: function (data) { return scheduleLabel(Number(data)); }},
                {data: 'sucursal', render: function (data) { return '<span class="payroll-branch"><i class="fas fa-map-marker-alt"></i>' + escapeHtml(data || translations.notAvailable) + '</span>'; }},
                {data: null, className: 'text-center', orderable: false, searchable: false, render: function (data, type, row) { return actionMenu(row); }}
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
                renderEmptyState();
            }
        });
    }

    function alertError(message) {
        if (window.Swal && window.Swal.fire) {
            window.Swal.fire({
                icon: 'error',
                text: message,
                confirmButtonColor: '#0e7490'
            });
        }
    }

    function initializeEvents() {
        $('#payroll-filters').on('submit', function (event) {
            event.preventDefault();
            hideTableError();
            table.ajax.reload(null, true);
        });

        $('#payroll-clear').on('click', function () {
            $('#payroll-status').val('1').trigger('change');
            $('#payroll-identification, #payroll-employee').val('');
            $('#payroll-branch').val('').trigger('change');
            hideTableError();
            table.ajax.reload(null, true);
        });

        $('#payroll-usage-log').on('click', function () {
            var button = $(this);
            button.prop('disabled', true);

            $.ajax({
                type: 'POST',
                url: config.logUrl,
                headers: {'X-CSRF-TOKEN': config.csrfToken},
                data: {tipo: config.usageLogType}
            }).done(function (html) {
                $('#div_modal_log').html(html);
                $('#modal_log_nomina').modal('show');
            }).fail(function () {
                alertError(translations.logLoadError);
            }).always(function () {
                button.prop('disabled', false);
            });
        });

        $(document).on('click', '.payroll-deactivate', function (event) {
            event.preventDefault();
            var url = $(this).data('deactivate-url');

            window.Swal.fire({
                title: translations.deactivateTitle,
                text: translations.deactivateText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc2626',
                cancelButtonColor: '#64748b',
                confirmButtonText: translations.deactivateConfirm,
                cancelButtonText: translations.cancel
            }).then(function (result) {
                if (!result.value) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: url,
                    headers: {'X-CSRF-TOKEN': config.csrfToken}
                }).done(function (response) {
                    window.Swal.fire({
                        icon: 'success',
                        text: response.message || translations.deactivateSuccess,
                        confirmButtonColor: '#0e7490'
                    });
                    table.ajax.reload(null, false);
                }).fail(function (xhr) {
                    var message = xhr.responseJSON && xhr.responseJSON.message ? xhr.responseJSON.message : translations.deactivateError;
                    alertError(message);
                });
            });
        });

        $('#modal_prestamos_empleado, #modal_otros_anticipos_empleado, #modal_saldos_iniciales').on('hidden.bs.modal', function () {
            $(this).removeData('bs.modal').find('.modal-content').empty();
            table.ajax.reload(null, false);
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
