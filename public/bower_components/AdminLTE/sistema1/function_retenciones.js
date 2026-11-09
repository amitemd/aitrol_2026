$(document).ready(function () {
    $('#tablaRetenciones').DataTable({
        "order": [
            [1, "desc"]
        ],
        responsive: true,
        displayLengtn: 25,
        bPaginate: false,
        info: false,
        searching: false,
    });
});
