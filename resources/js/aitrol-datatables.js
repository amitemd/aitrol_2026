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

const createServerSearch = (table) => {
    const form = document.createElement('form');
    const fieldId = `${table.id || 'aitrol-table'}-server-search`;
    const action = table.dataset.serverSearchUrl || window.location.pathname;
    const currentSearch = table.dataset.serverSearchValue || '';
    const currentPerPage = table.dataset.serverPerPage || '25';

    form.action = action;
    form.className = 'aitrol-server-search';
    form.method = 'get';
    form.setAttribute('role', 'search');

    const pageSizeLabel = document.createElement('label');
    pageSizeLabel.className = 'aitrol-server-search__page-size';
    pageSizeLabel.innerHTML = '<span>Mostrar</span>';

    const pageSize = document.createElement('select');
    pageSize.className = 'aitrol-server-search__select';
    pageSize.name = 'per_page';
    pageSize.setAttribute('aria-label', 'Registros por página');

    ['10', '25', '50'].forEach((value) => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        option.selected = value === currentPerPage;
        pageSize.append(option);
    });

    pageSizeLabel.append(pageSize);
    pageSizeLabel.append(document.createTextNode(' registros'));

    const searchGroup = document.createElement('div');
    searchGroup.className = 'aitrol-server-search__group';

    const searchLabel = document.createElement('label');
    searchLabel.className = 'sr-only';
    searchLabel.htmlFor = fieldId;
    searchLabel.textContent = 'Buscar en todos los registros';

    const searchIcon = document.createElement('i');
    searchIcon.className = 'fa fa-search aitrol-server-search__icon';
    searchIcon.setAttribute('aria-hidden', 'true');

    const searchInput = document.createElement('input');
    searchInput.autocomplete = 'off';
    searchInput.className = 'aitrol-server-search__input';
    searchInput.id = fieldId;
    searchInput.maxLength = 100;
    searchInput.name = 'q';
    searchInput.placeholder = 'Buscar en todos los registros';
    searchInput.type = 'search';
    searchInput.value = currentSearch;

    const submitButton = document.createElement('button');
    submitButton.className = 'aitrol-server-search__button';
    submitButton.innerHTML = '<i class="fa fa-search" aria-hidden="true"></i><span class="sr-only">Buscar</span>';
    submitButton.title = 'Buscar en todos los registros';
    submitButton.type = 'submit';

    searchGroup.append(searchLabel, searchIcon, searchInput, submitButton);
    form.append(pageSizeLabel, searchGroup);

    if (currentSearch !== '') {
        const clearUrl = new URL(action, window.location.origin);
        clearUrl.searchParams.set('per_page', currentPerPage);

        const clearLink = document.createElement('a');
        clearLink.className = 'aitrol-server-search__clear';
        clearLink.href = clearUrl.toString();
        clearLink.innerHTML = '<i class="fa fa-times" aria-hidden="true"></i><span class="sr-only">Limpiar búsqueda</span>';
        clearLink.title = 'Limpiar búsqueda';
        form.append(clearLink);
    }

    pageSize.addEventListener('change', () => form.submit());

    return form;
};

const initializeDataTable = (table) => {
    if (table.dataset.aitrolDataTableReady === 'true') {
        return;
    }

    const paging = readBoolean(table.dataset.paging, true);
    const searching = readBoolean(table.dataset.searching, true);
    const serverSearch = readBoolean(table.dataset.serverSearch, false);
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
            topEnd: serverSearch ? () => createServerSearch(table) : (searching ? 'search' : null),
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
