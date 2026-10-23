$(window).on("load", function () {

    $("#tablePersonas").DataTable({
        language: {
            decimal: "",
            emptyTable: $("#emptyTable").val(),
            info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
            infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
            infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " _MAX_ total " + $("#entradas").val() + ")",
            infoPostFix: "",
            thousands: ",",
            lengthMenu: $("#mostrar").val() + " _MENU_ " + $("#entradas").val(),
            loadingRecords: $("#loadingRecords").val() + "...",
            processing: $("#processing").val() + "...",
            search: $("#search").val(),
            zeroRecords: $("#zeroRecords").val(),
            paginate: {
                first: $("#first").val(),
                last: $("#last").val(),
                next: $("#next").val(),
                previous: $("#previous").val(),
            },
        },
        responsive: true,
        lengthChange: true,
        autoWidth: true,
        order: [[0, "desc"]],
        iDisplayLength: 100,
        dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
        buttons: [
            {
                extend: "copy",
                text: '<i class="fa fa-copy" title="Copy"></i>',
            },
            {
                extend: "excel",
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
            },
            {
                extend: "pdf",
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>',
            },
            {
                extend: "print",
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass("white-bg");
                    $(win.document.body).css("font-size", "10px");
                    $(win.document.body)
                        .find("table")
                        .addClass("compact")
                        .css("font-size", "inherit");
                },
            },
        ],
        ajax: {
            url:
                "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
            type: "post",
            headers: {
                "X-CSRF-TOKEN": $("#token").val(),
            },
            error(error) {
                console.log(error);
            },
        },
        columns: [
            {
                data: "id",
            },
            {
                data: "nombreCompleto",
            },
            {
                data: "email",
            },

            {
                data: "telefono",
            },
            {
                data: "email",
            },
            {
                data: "direccion",
            },

        ],

        columnDefs: [
            { "width": "20px", "targets": 0 },
            { "width": "150px", "targets": 1 },
            { "width": "200px", "targets": 2 },
            { "width": "100px", "targets": 3 },
            { "width": "200px", "targets": 4 },
            { "width": "250px", "targets": 5 },
        ],
    });
});

function agregarPersona() {
    console.log(1);
    window.open($("#base_url").val() + "/v3/personas/crearUsuario","_self");
}