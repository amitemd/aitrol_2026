(function (window, document, $) {
    'use strict';

    if (!$ || !$.fn || !$.fn.dataTable || !$.fn.dataTable.ext.buttons) {
        return;
    }

    var buttons = $.fn.dataTable.ext.buttons;
    var canExport = document.body.getAttribute('data-table-export') === '1';
    var exportButtonNames = ['copyHtml5', 'csvHtml5', 'excelHtml5', 'pdfHtml5', 'print'];

    function normalize(value) {
        return String(value || '')
            .replace(/<[^>]*>/g, ' ')
            .replace(/&nbsp;/gi, ' ')
            .replace(/\s+/g, ' ')
            .replace(/^\s+|\s+$/g, '')
            .toLowerCase();
    }

    function exportableColumn(index, data, node) {
        var header = normalize(node && node.textContent ? node.textContent : data);
        var excludedHeaders = {
            '': true,
            '...': true,
            'acción': true,
            'acciones': true,
            'action': true,
            'actions': true,
            'logo': true,
            'opción': true,
            'opciones': true
        };

        if (node && /(^|\s)(no-export|export-exclude)(\s|$)/.test(node.className || '')) {
            return false;
        }

        return !excludedHeaders[header];
    }

    function exportOptions(current) {
        var options = current || {};
        if (typeof options.columns === 'undefined') {
            options.columns = exportableColumn;
        }
        if (typeof options.stripHtml === 'undefined') {
            options.stripHtml = true;
        }
        if (typeof options.stripNewlines === 'undefined') {
            options.stripNewlines = true;
        }
        if (typeof options.decodeEntities === 'undefined') {
            options.decodeEntities = true;
        }
        if (typeof options.trim === 'undefined') {
            options.trim = true;
        }
        return options;
    }

    function findTable(node) {
        var i;
        var found;

        if (!node) {
            return null;
        }
        if (node.table && node.table.body) {
            return node;
        }
        if (!node.length) {
            return null;
        }
        for (i = 0; i < node.length; i += 1) {
            found = findTable(node[i] && node[i].content ? node[i].content : node[i]);
            if (found) {
                return found;
            }
        }
        return null;
    }

    function cellText(cell) {
        if (cell && typeof cell === 'object' && typeof cell.text !== 'undefined') {
            return normalize(cell.text);
        }
        return normalize(cell);
    }

    function calculateWidths(body, availableWidth) {
        var columnCount = body.length && body[0].length ? body[0].length : 0;
        var weights = [];
        var totalWeight = 0;
        var rowLimit = Math.min(body.length, 60);
        var column;
        var row;
        var length;
        var longestWord;
        var weight;

        for (column = 0; column < columnCount; column += 1) {
            length = 4;
            longestWord = 4;
            for (row = 0; row < rowLimit; row += 1) {
                var text = cellText(body[row][column]);
                var words = text.split(/\s+/);
                var wordIndex;
                length = Math.max(length, Math.min(text.length, 34));
                for (wordIndex = 0; wordIndex < words.length; wordIndex += 1) {
                    longestWord = Math.max(longestWord, Math.min(words[wordIndex].length, 20));
                }
            }
            weight = Math.max(5, Math.min(24, (length * 0.45) + (longestWord * 0.75)));
            weights.push(weight);
            totalWeight += weight;
        }

        return weights.map(function (columnWeight) {
            return Math.floor((availableWidth * columnWeight / totalWeight) * 10) / 10;
        });
    }

    function adaptPdf(doc) {
        var tableNode = findTable(doc.content);
        var body = tableNode && tableNode.table ? tableNode.table.body : [];
        var columnCount = body.length && body[0] ? body[0].length : 0;
        var useA3 = columnCount > 10;
        var fontSize = columnCount <= 7 ? 8.5 : (columnCount <= 10 ? 7.5 : (columnCount <= 14 ? 6.5 : 5.5));
        var availableWidth = useA3 ? 1154 : 806;
        var cellChromeWidth = columnCount * 7;
        var contentWidth = Math.max(availableWidth - cellChromeWidth, availableWidth * 0.72);

        doc.pageOrientation = 'landscape';
        doc.pageSize = useA3 ? 'A3' : 'A4';
        doc.pageMargins = [18, 24, 18, 26];
        doc.defaultStyle = doc.defaultStyle || {};
        doc.defaultStyle.fontSize = fontSize;
        doc.styles = doc.styles || {};
        doc.styles.title = $.extend({}, doc.styles.title || {}, {fontSize: 11, margin: [0, 0, 0, 8]});
        doc.styles.tableHeader = $.extend({}, doc.styles.tableHeader || {}, {
            bold: true,
            fontSize: fontSize + 0.5,
            color: 'white',
            fillColor: '#2d4154',
            alignment: 'center'
        });
        doc.styles.tableBodyEven = $.extend({}, doc.styles.tableBodyEven || {}, {fontSize: fontSize});
        doc.styles.tableBodyOdd = $.extend({}, doc.styles.tableBodyOdd || {}, {fontSize: fontSize});

        if (tableNode && columnCount) {
            tableNode.table.headerRows = 1;
            tableNode.table.dontBreakRows = true;
            tableNode.table.widths = calculateWidths(body, contentWidth);
            tableNode.layout = {
                hLineWidth: function () { return 0.35; },
                vLineWidth: function () { return 0.35; },
                hLineColor: function () { return '#b8c2cc'; },
                vLineColor: function () { return '#d4dbe1'; },
                paddingLeft: function () { return 3; },
                paddingRight: function () { return 3; },
                paddingTop: function () { return 3; },
                paddingBottom: function () { return 3; }
            };
        }

        if (!doc.footer) {
            doc.footer = function (currentPage, pageCount) {
                return {
                    text: 'Página ' + currentPage + ' de ' + pageCount,
                    alignment: 'right',
                    margin: [18, 0, 18, 0],
                    fontSize: 7,
                    color: '#64748b'
                };
            };
        }
    }

    function disableExports() {
        exportButtonNames.forEach(function (name) {
            if (buttons[name]) {
                buttons[name].action = function () { return false; };
            }
        });

        $(document).on('init.dt draw.dt', function () {
            $('.buttons-copy, .buttons-copyHtml5, .buttons-csv, .buttons-csvHtml5, .buttons-excel, .buttons-excelHtml5, .buttons-pdf, .buttons-pdfHtml5, .buttons-print')
                .attr({'aria-hidden': 'true', tabindex: '-1'})
                .prop('disabled', true);
        });
    }

    function configureExports() {
        exportButtonNames.forEach(function (name) {
            if (buttons[name]) {
                buttons[name].exportOptions = exportOptions(buttons[name].exportOptions);
            }
        });

        if (!buttons.pdfHtml5 || buttons.pdfHtml5.aitrolAdaptivePdf) {
            return;
        }

        var originalAction = buttons.pdfHtml5.action;
        buttons.pdfHtml5.orientation = 'landscape';
        buttons.pdfHtml5.exportOptions = exportOptions(buttons.pdfHtml5.exportOptions);
        buttons.pdfHtml5.action = function (event, dataTable, button, config) {
            var originalCustomize = config.customize;
            var adaptedConfig = $.extend({}, config, {
                orientation: 'landscape',
                exportOptions: exportOptions(config.exportOptions),
                customize: function (doc, buttonConfig, api) {
                    if (typeof originalCustomize === 'function') {
                        originalCustomize(doc, buttonConfig, api);
                    }
                    adaptPdf(doc);
                }
            });

            return originalAction.call(this, event, dataTable, button, adaptedConfig);
        };
        buttons.pdfHtml5.aitrolAdaptivePdf = true;
    }

    // Excel y PDF solo descargan sus bibliotecas cuando se solicita exportar.
    // La tabla conserva los mismos botones, datos y reglas de presentación.
    function configureLazyExports() {
        var assets = window.AitrolTableExportAssets;
        if (!assets || !window.Promise) return;
        var pending = {};

        function load(url, ready) {
            if (ready()) return Promise.resolve();
            if (pending[url]) return pending[url];
            pending[url] = new Promise(function (resolve, reject) {
                var script = document.createElement('script');
                var settled = false;
                var timer = window.setTimeout(function () { finish(false); }, 30000);
                function finish(success) {
                    if (settled) return;
                    settled = true;
                    window.clearTimeout(timer);
                    script.onload = script.onerror = null;
                    if (success && ready()) resolve();
                    else {
                        if (script.parentNode) script.parentNode.removeChild(script);
                        reject(new Error('Export asset unavailable'));
                    }
                }
                script.src = url;
                script.async = true;
                script.onload = function () { finish(true); };
                script.onerror = function () { finish(false); };
                document.head.appendChild(script);
            }).catch(function (error) {
                delete pending[url]; // Permitir reintentar después de un fallo de red.
                throw error;
            });
            return pending[url];
        }

        function zipReady() { return typeof window.JSZip === 'function'; }
        function pdfReady() { return window.pdfMake && typeof window.pdfMake.createPdf === 'function'; }
        function fontsReady() { return pdfReady() && window.pdfMake.vfs && Object.keys(window.pdfMake.vfs).length > 0; }

        function wrap(name, ready, prepare) {
            var definition = buttons[name];
            if (!definition || definition.aitrolLazyExport) return;
            var action = definition.action;
            var available = definition.available;
            definition.available = function () {
                if (ready()) return available.apply(this, arguments);
                return typeof window.FileReader !== 'undefined' && typeof window.Blob !== 'undefined'
                    && (name !== 'excelHtml5' || typeof window.XMLSerializer !== 'undefined');
            };
            definition.action = function (event, table, button, config) {
                if (ready()) return action.call(this, event, table, button, config);
                var context = this;
                var node = $(button);
                if (node.data('aitrol-export-loading')) return;
                node.data('aitrol-export-loading', true).prop('disabled', true).attr('aria-busy', 'true');
                context.processing(true);
                return prepare().then(function () {
                    if (!available.call(context, table, config)) throw new Error('Export unavailable');
                    // No iniciar una descarga si la tabla se cerró durante la carga.
                    if (!$.fn.dataTable.isDataTable(table.table().node())) {
                        context.processing(false);
                        return;
                    }
                    return action.call(context, event, table, button, config);
                }).catch(function () {
                    context.processing(false);
                    var message = assets.error || 'No se pudo preparar la exportación. Inténtalo nuevamente.';
                    if (window.Swal && window.Swal.fire) window.Swal.fire({icon: 'error', text: message});
                    else window.alert(message);
                }).then(function () {
                    node.removeData('aitrol-export-loading').prop('disabled', false).removeAttr('aria-busy');
                });
            };
            definition.aitrolLazyExport = true;
        }

        wrap('excelHtml5', zipReady, function () { return load(assets.zip, zipReady); });
        wrap('pdfHtml5', fontsReady, function () {
            return load(assets.pdf, pdfReady).then(function () { return load(assets.fonts, fontsReady); });
        });
    }

    if (canExport) {
        configureExports();
        configureLazyExports();
    } else {
        disableExports();
    }
}(window, document, window.jQuery));
