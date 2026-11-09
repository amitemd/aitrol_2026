$(document).ready(function () {
    $('#tableDocumentos').DataTable({
        language: {
            "decimal": "",
            "emptyTable": "No hay información",
            "info": "Mostrando _START_ a _END_ de _TOTAL_ Entradas",
            "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
            "infoFiltered": "(Filtrado de _MAX_ total entradas)",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": "Mostrar _MENU_ Entradas",
            "loadingRecords": "Cargando...",
            "processing": "Procesando...",
            "search": "Buscar:",
            "zeroRecords": "Sin resultados encontrados",
            "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
            }
        },
        "order": [
            [0, "desc"]
        ],
        dom: '<"toolbar">Bfrltip',
        buttons: [{
                extend: 'copy'
            },
            {
                extend: 'csv'
            },
            {
                extend: 'excel',
                title: 'prueba'
            },
            {
                extend: 'pdf',
                title: 'prueba'
            },
            {
                extend: 'print',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table')
                        .addClass('compact')
                        .css('font-size', 'inherit');
                }
            }
        ],
        sAjaxSource: "/historialdocumentos/documentosElectronicosjs",
        displayLengtn: 25,
        bPaginate: true,
        responsive: true,
        aoColumns: [{
            data: 'id'
        }, {
            data: 'nombre_paso'
        }, {
            data: 'nombre_documento'
        }, {
            render: function (data, type, row) {
                return row.establecimiento + '-' + row.emision + '-' + row.secuencial;
            }
        }, {
            data: 'ruc_receptor'
        }, {
            data: 'ruc_emisor'
        }, {
            data: 'clave_acceso'
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                var n1 = row.nombre1 !== null ? row.nombre1 : '';
                var n2 = row.nombre2 !== null ? row.nombre2 : '';
                return n1 + ' ' + n2;
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<button type="button" class="btn btn-info"><i class="fa fa-eye"></i> Ver</button>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<button type="button" class="btn btn-success"><i class="fa fa-eye"></i> Ver</button>';
            }
        }, {
            className: 'text-center',
            render: function (data, type, row) {
                return '<button type="button" id="btnerrores" onclick="vererror(\'' + row.id + '\')" class="btn btn-danger btnerrores"><i class="fa fa-eye"></i> Historial</button>';
            }
        }, ]
    });
    console.log("ASD");
});

function vererror(id) {
    $.ajax({
        url: '',
        type: 'post',
        data: {
            _token: '',
            
        },
        dataType: 'json',
        success: function (json) { 
            console.log(json);
            $('#modalErrores').modal('show');
        }
    });
    
}