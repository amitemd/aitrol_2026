import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import JSZip from 'jszip';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'datatables.net-buttons-dt/css/buttons.dataTables.css';
import '../css/aitrol-datatables.css';

if (DataTable.Buttons?.jszip) {
    DataTable.Buttons.jszip(JSZip);
}

let pdfMakePromise;

const loadPdfMake = () => {
    if (!pdfMakePromise) {
        pdfMakePromise = Promise.all([
            import('pdfmake/build/pdfmake.js'),
            import('pdfmake/build/vfs_fonts.js'),
        ])
            .then(([pdfMakeModule, pdfFontsModule]) => {
                const pdfMake = pdfMakeModule.default;

                pdfMake.addVirtualFileSystem(pdfFontsModule.default);
                DataTable.Buttons.pdfMake(pdfMake);

                return pdfMake;
            })
            .catch((error) => {
                pdfMakePromise = undefined;
                throw error;
            });
    }

    return pdfMakePromise;
};

const spanishLanguage = {
    decimal: ',',
    thousands: '.',
    emptyTable: 'No hay información disponible',
    info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
    infoEmpty: 'Mostrando 0 a 0 de 0 registros',
    infoFiltered: '(filtrado de _MAX_ registros)',
    lengthMenu: 'Mostrar _MENU_ registros',
    loadingRecords: 'Cargando…',
    processing: 'Procesando…',
    search: 'Buscar:',
    searchPlaceholder: 'Buscar en la tabla',
    zeroRecords: 'No se encontraron resultados',
    buttons: {
        copyTitle: 'Datos copiados',
        copySuccess: {
            _: '%d filas copiadas',
            1: '1 fila copiada',
        },
    },
    paginate: {
        first: 'Primero',
        last: 'Último',
        next: 'Siguiente',
        previous: 'Anterior',
    },
};

const readBoolean = (value, fallback) => {
    if (value === undefined) {
        return fallback;
    }

    return value !== 'false';
};

const initializeDataTable = (table) => {
    if (table.dataset.aitrolDataTableReady === 'true') {
        return;
    }

    const paging = readBoolean(table.dataset.paging, true);
    const searching = readBoolean(table.dataset.searching, true);
    const info = readBoolean(table.dataset.info, true);
    const exportName = table.dataset.exportName;
    const orderColumn = Number.parseInt(table.dataset.orderColumn ?? '', 10);
    const orderDirection = table.dataset.orderDirection === 'asc' ? 'asc' : 'desc';
    const exportOptions = {
        modifier: {
            order: 'applied',
            page: 'all',
            search: 'applied',
        },
    };
    const buttons = exportName
        ? [
            {
                extend: 'copyHtml5',
                text: '<i class="fa fa-clone" aria-hidden="true"></i><span class="sr-only">Copiar</span>',
                titleAttr: 'Copiar datos',
                className: 'aitrol-export-button aitrol-export-button--copy',
                exportOptions,
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i><span class="sr-only">Excel</span>',
                titleAttr: 'Exportar a Excel',
                className: 'aitrol-export-button aitrol-export-button--excel',
                title: exportName,
                footer: true,
                exportOptions,
            },
            {
                extend: 'pdfHtml5',
                available: () => true,
                text: '<i class="fa fa-file-pdf-o" aria-hidden="true"></i><span class="sr-only">PDF</span>',
                titleAttr: 'Exportar a PDF',
                className: 'aitrol-export-button aitrol-export-button--pdf',
                title: exportName,
                footer: true,
                orientation: 'landscape',
                pageSize: 'A3',
                exportOptions,
                async: 100,
                action: async function (event, dataTable, button, config, done) {
                    try {
                        await loadPdfMake();
                        DataTable.ext.buttons.pdfHtml5.action.call(this, event, dataTable, button, config, done);
                    } catch (error) {
                        console.error('No se pudo generar el PDF de la tabla.', error);
                        window.alert('No se pudo generar el PDF. Inténtelo nuevamente.');
                        done();
                    }
                },
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" aria-hidden="true"></i><span class="sr-only">Imprimir</span>',
                titleAttr: 'Imprimir tabla',
                className: 'aitrol-export-button aitrol-export-button--print',
                title: exportName,
                footer: true,
                exportOptions,
            },
        ]
        : [];

    const options = {
        autoWidth: false,
        info,
        language: spanishLanguage,
        ordering: true,
        paging,
        responsive: true,
        searching,
        layout: {
            topStart: buttons.length > 0 ? { buttons } : null,
            topEnd: searching ? 'search' : null,
            bottomStart: info ? 'info' : null,
            bottomEnd: paging ? 'paging' : null,
        },
    };

    if (Number.isInteger(orderColumn)) {
        options.order = [[orderColumn, orderDirection]];
    }

    table.dataset.aitrolDataTableReady = 'true';
    new DataTable(table, options);
};

const initializeAitrolDataTables = () => {
    document.querySelectorAll('table[data-aitrol-datatable]').forEach(initializeDataTable);
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAitrolDataTables, { once: true });
} else {
    initializeAitrolDataTables();
}
