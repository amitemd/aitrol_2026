import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';
import 'datatables.net-buttons-dt';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import JSZip from 'jszip';
import 'datatables.net-dt/css/dataTables.dataTables.css';
import 'datatables.net-responsive-dt/css/responsive.dataTables.css';
import 'datatables.net-buttons-dt/css/buttons.dataTables.css';
import '../css/aitrol-datatables.css';

if (DataTable.Buttons?.jszip) {
    DataTable.Buttons.jszip(JSZip);
}

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
    const buttons = exportName
        ? [{
            extend: 'excelHtml5',
            text: '<i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar Excel',
            className: 'aitrol-export-button',
            title: exportName,
            footer: true,
        }]
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
