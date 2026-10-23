(function ($, Chart, config) {
    'use strict';

    if (!$ || !Chart || !config) {
        return;
    }

    var colors = { blue: '#1976b9', orange: '#f39c12', teal: '#2f8f83', navy: '#28465f' };
    var detailTypes = ['facturado', 'emitido', 'cotizacion'];
    var detailParams = {};

    if ($.fn.select2) {
        $('#labs-filter-form select').select2({
            width: '100%',
            language: {
                noResults: function () { return 'No se encontraron resultados'; },
                searching: function () { return 'Buscando...'; }
            }
        });
    }

    function money(value) {
        var number = Number(value || 0).toFixed(2).split('.');
        number[0] = number[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return '$' + number.join('.');
    }

    function formParams() {
        var params = {};
        $.each($('#labs-filter-form').serializeArray(), function (_, field) {
            if (field.value !== '') {
                params[field.name] = field.value;
            }
        });
        return params;
    }

    function buildUrl(base, params) {
        var query = $.param(params);
        return base + (query ? '?' + query : '');
    }

    function parseParams(url) {
        var query = url.indexOf('?') >= 0 ? url.split('?')[1] : '';
        var params = {};
        $.each(query.split('&'), function (_, pair) {
            if (!pair) { return; }
            var parts = pair.split('=');
            params[decodeURIComponent(parts[0])] = decodeURIComponent((parts[1] || '').replace(/\+/g, ' '));
        });
        return params;
    }

    function loadDetail(url) {
        detailParams = parseParams(url);
        $('#labs-detail-content').html('<div class="labs-loading"><i class="fa fa-spinner fa-spin"></i> Cargando detalle...</div>');
        $.get(url).done(function (html) {
            $('#labs-detail-content').html(html);
        }).fail(function () {
            $('#labs-detail-content').html('<div class="alert alert-danger">No fue posible cargar el detalle. Intente nuevamente.</div>');
        });
    }

    function openDetail(type, insurance, extra) {
        var params = formParams();
        params.tipo = type || 'todos';
        if (insurance === null) {
            params.seguro_sin_asignar = 1;
        } else if (insurance !== undefined && insurance !== '') {
            params.seguro_detalle = insurance;
        }
        $.extend(params, extra || {});
        delete params.page;
        $('#labs-detail-modal').modal('show');
        loadDetail(buildUrl(config.detailUrl, params));
    }

    $(document).on('click', '.js-labs-detail', function () {
        var insurance = $(this).attr('data-insurance');
        if ($(this).attr('data-no-insurance') === '1') {
            insurance = null;
        }
        openDetail($(this).data('detail'), insurance);
    });

    $('.js-labs-export').on('click', function () {
        var params = formParams();
        params.tipo = $(this).data('detail') || 'todos';
        window.location.href = buildUrl(config.exportUrl, params);
    });

    $(document).on('click', '#labs-detail-content .pagination a, #labs-detail-content .labs-detail-table thead a', function (event) {
        event.preventDefault();
        loadDetail($(this).attr('href'));
    });

    $(document).on('submit', '#labs-detail-search', function (event) {
        event.preventDefault();
        detailParams.busqueda = $('#labs-busqueda').val();
        delete detailParams.page;
        loadDetail(buildUrl(config.detailUrl, detailParams));
    });

    var commonTooltip = {
        callbacks: {
            label: function (tooltip, data) {
                var value = data.datasets[tooltip.datasetIndex].data[tooltip.index];
                return ' ' + data.datasets[tooltip.datasetIndex].label + ': ' + money(value);
            }
        }
    };

    var statusTooltip = {
        callbacks: {
            label: function (tooltip, data) {
                var value = Number(data.datasets[0].data[tooltip.index] || 0);
                var total = data.datasets[0].data.reduce(function (sum, item) { return sum + Number(item || 0); }, 0);
                var percentage = total > 0 ? ((value / total) * 100).toFixed(2) : '0.00';
                return ' ' + data.labels[tooltip.index] + ': ' + money(value) + ' (' + percentage + '%)';
            }
        }
    };

    var statusCanvas = document.getElementById('labs-status-chart');
    if (statusCanvas) {
        new Chart(statusCanvas, {
            type: 'doughnut',
            data: { labels: ['Facturado', 'Emitido', 'Cotización'], datasets: [{ data: config.status.values, backgroundColor: [colors.blue, colors.teal, colors.orange], borderColor: '#ffffff', borderWidth: 3 }] },
            options: {
                responsive: true, maintainAspectRatio: false, cutoutPercentage: 62,
                legend: { display: false }, tooltips: statusTooltip,
                onClick: function (event) {
                    var points = this.getElementAtEvent(event);
                    if (points.length) { openDetail(detailTypes[points[0]._index]); }
                }
            }
        });
    }

    var insuranceCanvas = document.getElementById('labs-insurance-chart');
    if (insuranceCanvas) {
        new Chart(insuranceCanvas, {
            type: 'bar',
            data: {
                labels: config.insurance.labels,
                datasets: [
                    { label: 'Facturado', data: config.insurance.billed, backgroundColor: colors.blue },
                    { label: 'Emitido', data: config.insurance.issued, backgroundColor: colors.teal },
                    { label: 'Cotizado', data: config.insurance.quoted, backgroundColor: colors.orange }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false, tooltips: commonTooltip,
                legend: { position: 'bottom', labels: { boxWidth: 11, fontSize: 10 } },
                scales: {
                    xAxes: [{ gridLines: { display: false }, ticks: { fontSize: 9, autoSkip: false, maxRotation: 35, minRotation: 0 } }],
                    yAxes: [{ ticks: { beginAtZero: true, fontSize: 9, callback: function (value) { return money(value); } } }]
                },
                onClick: function (event) {
                    var points = this.getElementAtEvent(event);
                    if (!points.length) { return; }
                    var point = points[0];
                    openDetail(detailTypes[point._datasetIndex], config.insurance.ids[point._index]);
                }
            }
        });
    }

    var trendCanvas = document.getElementById('labs-trend-chart');
    if (trendCanvas) {
        var singleTrendPeriod = config.trend.labels.length === 1;
        new Chart(trendCanvas, {
            type: 'line',
            data: {
                labels: config.trend.labels,
                datasets: [
                    { type: singleTrendPeriod ? 'bar' : 'line', label: 'Valor facturado', data: config.trend.billed, borderColor: colors.blue, backgroundColor: singleTrendPeriod ? colors.blue : 'rgba(25,118,185,.08)', yAxisID: 'money', fill: false, lineTension: .2 },
                    { type: singleTrendPeriod ? 'bar' : 'line', label: 'Valor emitido', data: config.trend.issued, borderColor: colors.teal, backgroundColor: singleTrendPeriod ? colors.teal : 'rgba(47,143,131,.08)', yAxisID: 'money', fill: false, lineTension: .2 },
                    { type: singleTrendPeriod ? 'bar' : 'line', label: 'Valor cotizado', data: config.trend.quoted, borderColor: colors.orange, backgroundColor: singleTrendPeriod ? colors.orange : 'rgba(243,156,18,.08)', yAxisID: 'money', fill: false, lineTension: .2 },
                    { label: 'N.º facturadas', data: config.trend.billedCount, borderColor: colors.blue, backgroundColor: colors.blue, borderDash: [5, 4], yAxisID: 'count', fill: false, pointRadius: singleTrendPeriod ? 5 : 2 },
                    { label: 'N.º emitidas', data: config.trend.issuedCount, borderColor: colors.teal, backgroundColor: colors.teal, borderDash: [5, 4], yAxisID: 'count', fill: false, pointRadius: singleTrendPeriod ? 5 : 2 },
                    { label: 'N.º cotizaciones', data: config.trend.quotedCount, borderColor: colors.orange, backgroundColor: colors.orange, borderDash: [5, 4], yAxisID: 'count', fill: false, pointRadius: singleTrendPeriod ? 5 : 2 }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                legend: { position: 'bottom', labels: { boxWidth: 11, fontSize: 10 } },
                tooltips: {
                    mode: 'index', intersect: false,
                    callbacks: {
                        label: function (tooltip, data) {
                            var dataset = data.datasets[tooltip.datasetIndex];
                            var value = dataset.data[tooltip.index];
                            return ' ' + dataset.label + ': ' + (dataset.yAxisID === 'money' ? money(value) : value);
                        }
                    }
                },
                scales: {
                    xAxes: [{ offset: singleTrendPeriod, gridLines: { display: false }, ticks: { fontSize: 9 } }],
                    yAxes: [
                        { id: 'money', position: 'left', ticks: { beginAtZero: true, fontSize: 9, callback: function (value) { return money(value); } } },
                        { id: 'count', position: 'right', gridLines: { drawOnChartArea: false }, ticks: { beginAtZero: true, precision: 0, fontSize: 9 } }
                    ]
                },
                onClick: function (event) {
                    var points = this.getElementAtEvent(event);
                    if (!points.length) { return; }
                    var point = points[0];
                    var range = config.trend.ranges[point._index] || {};
                    openDetail(detailTypes[point._datasetIndex % 3], undefined, {
                        fecha_desde: range.desde,
                        fecha_hasta: range.hasta
                    });
                }
            }
        });
    }
})(window.jQuery, window.Chart, window.dashboardLabsConfig);
