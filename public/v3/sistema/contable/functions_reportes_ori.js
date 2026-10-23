var sumaRenta = 0;
$(document).ready(function () {
    if (($('#submodulo_').val() == 'ventas' && $('#extra_').val() == 'ventasnetas') ||
        ($('#submodulo_').val() == 'proveedores' && $('#extra_').val() == 'notadecredito') ||
        ($('#submodulo_').val() == 'proveedores' && $('#extra_').val() == 'retencionesproveedores') ||
        ($('#submodulo_').val() == 'proveedores' && $('#extra_').val() == 'deudasvspagos') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'librodiario') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'mayorizacion') ||
        ($('#submodulo_').val() == 'clientes' && $('#extra_').val() == 'deudasvspagos') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'balancedecomprobacion') ||
        ($('#submodulo_').val() == 'contables' && $('#extra_').val() == 'flujodeefectivo')) {
        $(".botonMenu").trigger("click");
    }
    var tabla = '';
    var tableNotasCreditoProveedor = '';
    var tableNotasDebitoClientes = '';
    var tableNotasDebitoProveedores = '';
    var tablaRetencionesProveedor = '';
    var tablaDeudavsPagosClientes = '';
    var tablaDeudasvsPagosProveedores = '';
    var tableClientesReportesRetenciones = '';
    var tableNotasCreditoClientes = '';
    var tablaDeudasPendientes = '';
    var tableBalanceComprobacion = '';
    var tableLibroDiario = '';
    try {
        $("#tableProveedoresEgresoMasivo").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/egresomasivojs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                }, error(error) {
                    //window.open('v3/admin', '_self');
                    console.log(error);
                }
            },
            columns: [{
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'id'
            }, {
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">'+$('#activo').val()+'</label>' : '<label class="text-danger">'+$('#inactivo').val()+'</label>';
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'identificacion',
                render: function (data, type, row) {
                    var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $('#activar').val();
                    if (row.estado == 1) {
                        accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $('#desactivar').val();
                    }
                    var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $('#colorBoton').val() + ' btn-sm"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $('#colorBoton').val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verEgresoMasivo(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + '\',\'' + row.estado + '\',\'EgresoMasivo\');">' + accion + '</a></div></div>';
                    return botones;
                }
            }],

        });
       
        tabla = $("#tableVentasVentasNetas").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasCreditoProveedor = $("#tableNotasCreditoProveedor").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasDebitoClientes = $("#tableNotasDebitoClientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasDebitoProveedores = $("#tableNotasDebitoProveedores").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaRetencionesProveedor = $("#tablaRetencionesProveedor").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudavsPagosClientes = $("#tablaDeudavsPagosClientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudasvsPagosProveedores = $("#tablaDeudasvsPagosProveedores").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableClientesReportesRetenciones = $("#tableClientesReportesRetenciones").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableNotasCreditoClientes = $("#tableNotasCreditoClientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tablaDeudasPendientes = $("#tablaDeudasPendientes").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
        tableLibroDiario = $("#tableLibroDiario").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleLibroDiario",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                },
            },
            columns: [{
                data: 'id',
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id + '\');">' + row.id + '</a>';
                }
            }, {
                className: 'text-center',
                data: 'fecha_asiento',
                render: function (data, type, row) {
                    return getfecha(row.fecha_asiento);
                }
            }, {
                className: 'text-center',
                data: 'plan',
            }, {
                data: 'nombre_cuenta'
            }, {
                className: 'text-right',
                data: 'debe',
                render: function (data, type, row) {
                    return row.debe;
                }
            }, {
                className: 'text-right',
                data: 'haber',
                render: function (data, type, row) {
                    $('#totalDebe').html(row.total_debe);
                    $('#totalHaber').html(row.total_haber);
                    return row.haber;
                }
            }, {
                data: 'observacion'
            }, {
                className: 'text-center',
                data: 'usuariocrea'
            }, {
                className: 'text-center',
                data: 'usuariomod',
            }],
        });
        tableBalanceComprobacion = $("#tableBalanceComprobacion").DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>'
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }],
        });
    } catch (error) { console.log(error); }
    $('#tipo').change(function () {
        if ($('#tipo').val() != '' && $('#tipo').val() == 4) {
            $('#divOculto').show();
        } else {
            $('#divOculto').hide();
        }
    });
    
    $(".select2_proveedor").select2({
        width: "100%",
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/buscarProveedorCombo",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });

    $('#id_cliente').select2({
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/utilidades/clienteCombo",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }, error(error) {

            },
            cache: true,
        }
    });
    $('#id_vendedor').select2({
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/utilidades/vendedorCombo",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }, error(error) {

            },
            cache: true,
        }
    });
    $('#estado_').select2({
        allowClear: true,
        placeholder: "--Seleccione--",
    });
    $('#id_proveedor').select2({
        allowClear: true,
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccione--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/utilidades/proveedorCombo",
            type: "post",
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            dataType: 'json',
            delay: 250,
            data: function (params) {
                return {
                    term: params.term
                };
            },
            processResults: function (response) {
                return {
                    results: response
                };
            }, error(error) {

            },
            cache: true,
        }
    });
});
function buscarBalanceComprobacion() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableBalanceComprobacion').dataTable().fnClearTable();
    $('#tableBalanceComprobacion').dataTable().fnDestroy();
    $('#tableBalanceComprobacion').DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: false,
        autoWidth: true,
        info: true,
        aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
        iDisplayLength: 500,
        dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
        order: [[1, "asc"]],
        columnDefs: [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 5, 6] },
        ],
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-pdf" title="PDF"></i>'
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleBalanceComprobacion",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                d: $('#desde').val(),
                h: $('#hasta').val(),
            },
        },
        columns: [{
            className: 'text-center',
            data: 'plan',
        }, {
            data: 'nombre',
        }, {
            className: 'text-right',
            data: 'debe',
        }, {
            className: 'text-right',
            data: 'haber',
        }, {
            className: 'text-right',
            data: 'saldo_deudor',
        }, {
            className: 'text-right',
            data: 'saldo_acreedor',
        }],
    });
}
function buscarVentasNetas() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#v').html(0);
        $('#d').html(0);
        $('#vn').html(0);
        $('#nc').html(0);
        $('#vnf').html(0);
        $('#tableVentasVentasNetas').dataTable().fnClearTable();
        $('#tableVentasVentasNetas').dataTable().fnDestroy();
        $('#tableVentasVentasNetas').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'pdf',
                title: tituloExcel,
                text: '<i class="fas fa-file-pdf" title="PDF"></i>'
            }, {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/ventasnetasjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    cliente: $('#id_cliente').val(),
                    vendedor: $('#id_vendedor').val(),
                    estado: $('#estado_').val(),
                },
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'nro_comprobante'
            }, {
                data: 'id_cliente'
            }, {
                data: 'nombre_cliente'
            }, {
                data: 'vendedor'
            }, {
                className: 'text-right',
                data: 'id',
                render: function (data, type, row) {
                    return parseFloat(row.subtotal).toFixed(2);
                }
            }, {
                className: 'text-right',
                data: 'descuento'
            }, {
                className: 'text-right',
                data: 'vn'
            }, {
                className: 'text-right',
                data: 'nc'
            }, {
                className: 'text-right',
                data: 'vnf'
            }, {
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }
            }, {
                data: 'usuariocrea'
            }, {
                data: 'usuariomod',
                render: function (data, type, row) {
                    $('#v').html(parseFloat(row.total_subtotal).toFixed(2));
                    $('#d').html(parseFloat(row.total_descuento).toFixed(2));
                    $('#vn').html(parseFloat(row.total_vn).toFixed(2));
                    $('#nc').html(parseFloat(row.total_nc).toFixed(2));
                    $('#vnf').html(parseFloat(parseFloat(row.total_vn) - parseFloat(row.total_nc)).toFixed(2));
                    return row.usuariomod;
                }
            }],
        });
    }
}
function agregarEgresoMasivo(id) {
    limpiarModal();
    $('#id').val('');
    $('#cheestado').attr('checked', true);
    $('#estado').val(1);
    $('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo Comprobante de egreso masivo');
    $('#myModal').modal('toggle');
}
function getPerdidasGanancias() {
    $('#frm_').submit();
}
function buscarNotasdeCreditoProveedor() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#subtotal_0').html('0.00');
        $('#subtotal_12').html('0.00');
        $('#subtotal').html('0.00');
        $('#impuesto').html('0.00');
        $('#total').html('0.00');
        $('#tableNotasCreditoProveedor').dataTable().fnClearTable();
        $('#tableNotasCreditoProveedor').dataTable().fnDestroy();
        $('#tableNotasCreditoProveedor').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeCreditoProveedorjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    proveedor: $('#id_proveedor').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'secuencia_factura'
            }, {
                data: 'nombrecomercial'
            }, {
                className: 'text-center',
                data: 'numero'
            }, {
                data: 'concepto'
            }, {
                className: 'text-right',
                data: 'subtotal_0'
            }, {
                className: 'text-right',
                data: 'subtotal_12'
            }, {
                className: 'text-right',
                data: 'subtotal'
            }, {
                className: 'text-right',
                data: 'impuesto'
            }, {
                className: 'text-right',
                data: 'valor_contable',
                render: function (data, type, row) {
                    $('#subtotal_0').html(row.sumSubtotal0);
                    $('#subtotal_12').html(row.sumSubtotal12);
                    $('#subtotal').html(row.sumSubtotal);
                    $('#impuesto').html(row.sumImpuesto);
                    $('#total').html(row.sumTotal);
                    return row.valor_contable;
                }
            },],
        });
    }
}
function buscarNotasdeCreditoClientes() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    } else {
        $('#total_credito').html('0.00');
        $('#total_deuda').html('0.00');
        $('#total_abono').html('0.00');
        $('#tableNotasCreditoClientes').dataTable().fnClearTable();
        $('#tableNotasCreditoClientes').dataTable().fnDestroy();
        $('#tableNotasCreditoClientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeCreditoClientesjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_cliente: $('#id_cliente').val(),
                }
            },

            columns: [
                {
                    className: "text-center",
                    data: "id",
                },
                {
                    className: "text-right",
                    data: "id_asiento_cabecera",
                },
                {
                    className: "text-right",
                    data: "nombre_cliente",
                },
                {
                    className: "text-center",
                    data: "fecha",
                },
                {
                    className: "text-center",
                    data: "tipo",
                },
                {
                    className: "text-center",
                    data: "nro_comprobante",
                },
                {
                    className: "text-center",
                    data: "concepto",
                },
                {
                    className: "text-center",
                    data: "total_credito",
                },
                {
                    className: "text-center",
                    data: "total_deudas",
                },
                {
                    className: "text-center",
                    data: "total_abonos",
                },

                {
                    className: "text-center",
                    data: 'nombre_crea'
                },
                {
                    className: 'text-center',
                    data: 'estado',
                    render: function (data, type, row) {
                        var estado = '<label class="text-success">'+$('#activo').val()+'</label>';
                        if (row.estado == 0) {
                            estado = '<label class="text-danger">'+$('#inactivo').val()+'</label>';
                        }
                        return estado;
                    }
                },
                {
                    className: 'text-center',
                    data: 'electronica',
                    render: function (data, type, row) {
                        if (row.total_credito !== null) {
                            $('#total_credito').html(parseFloat(row.total_credito).toFixed(2));
                        }
                        if (row.total_deudas !== null) {
                            $('#total_deuda').html(parseFloat(row.total_deudas).toFixed(2));
                        }
                        if (row.total_abonos !== null) {
                            $('#total_abono').html(parseFloat(row.total_abonos).toFixed(2));
                        }
                        var electronica = '<label class="text-success">Electronica</label>';
                        if (row.electronica == 0) {
                            electronica = '<label class="text-danger">Normal</label>';
                        }
                        return electronica;
                    }
                },
            ],
        });
    }

}
function buscarClientesRetenciones() {

    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    } else {
        console.log("sdasd");
        $('#tableClientesReportesRetenciones').dataTable().fnClearTable();
        $('#tableClientesReportesRetenciones').dataTable().fnDestroy();
        $('#tableClientesReportesRetenciones').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/retencionesFunctionjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_cliente: $('#id_cliente').val(),
                }
            },

            columns: [
                {
                    className: "text-center",
                    data: "fecha",
                },
                {
                    className: "text-center",
                    data: "nombre",
                },
                {
                    className: "text-center",
                    data: "descripcion",
                },
                {
                    className: "text-center",
                    data: "nro_comprobante",
                },
                {
                    className: "text-center",
                    data: "valor_fuente",
                },
                {
                    className: "text-center",
                    data: "valor_iva",
                },
                {
                    className: 'text-center',
                    data: 'total',
                },
                {
                    className: 'text-center',
                    data: 'estado',
                    render: function (data, type, row) {
                        var estado = '<label class="text-success">Activo</label>';
                        if (row.estado == 0) {
                            estado = '<label class="text-danger">Inactivo</label>';
                        }
                        return estado;
                    }
                },
                {
                    className: "text-center",
                    data: "nombre_crea",
                },
            ],
        });
    }

}
function buscarNotasdeDebitoClientes() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    }
    else {
        $('#total').html('0.00');
        $('#tableNotasDebitoClientes').dataTable().fnClearTable();
        $('#tableNotasDebitoClientes').dataTable().fnDestroy();
        $('#tableNotasDebitoClientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeDebitoClientesjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_cliente: $('#id_cliente').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'numero'
            }, {
                data: 'cliente'
            }, {
                data: 'concepto'
            }, {
                className: 'text-right',
                data: 'valor_contable',
                render: function (data, type, row) {
                    $('#total').html(row.total);
                    return row.valor_contable;
                },
            }, {
                className: 'text-center',
                data: 'ucnombre',
                render: function (data, type, row) {
                    var cadena = row.ucnombre;
                    var letra = cadena.charAt(0);
                    return letra + ". " + row.ucapellido;
                },
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">Activo</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">Inactivo</label>';
                    }
                    return estado;
                }
            }],
        });
    }

}
function buscarNotasdeDebitoProveedores() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de fin...');
    }
    else {
        $('#total').html('0.00');
        $('#tableNotasDebitoProveedores').dataTable().fnClearTable();
        $('#tableNotasDebitoProveedores').dataTable().fnDestroy();
        $('#tableNotasDebitoProveedores').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                }
            }], "ajax": {
                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/notasdeDebitoProveedoresjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    id_proveedor: $('#id_proveedor').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'secuencia'
            }, {
                className: 'text-right',
                data: 'id_asiento_cabecera'
            }, {
                data: 'concepto'
            }, {
                className: 'text-center',
                data: 'fecha_factura'
            }, {
                data: 'proveedor'
            }, {
                className: 'text-right',
                data: 'valor_contable',
                render: function (data, type, row) {
                    $('#total').html(row.total);
                    return row.valor_contable;
                },
            }, {
                className: 'text-center',
                data: 'ucnombre',
                render: function (data, type, row) {
                    var cadena = row.ucnombre;
                    var letra = cadena.charAt(0);
                    return letra + ". " + row.ucapellido;
                },
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = '<label class="text-success">Activo</label>';
                    if (row.estado == 0) {
                        estado = '<label class="text-danger">Inactivo</label>';
                    }
                    return estado;
                }
            }],
        });
    }

}
function buscarRetencionesProveedor() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#rfir').html('0.00');
        $('#rfiva').html('0.00');
        $('#tablaRetencionesProveedor').dataTable().fnClearTable();
        $('#tablaRetencionesProveedor').dataTable().fnDestroy();
        $('#tablaRetencionesProveedor').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            "responsive": true,
            "lengthChange": false,
            "autoWidth": true,
            info: true,
            "aLengthMenu": [[2, 4, 8, -1], [2, 4, 8, "All"]],
            "iDisplayLength": 25,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                },
                footer: true,
            }], "ajax": {

                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/retencionesProveedorjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    proveedor: $('#id_proveedor').val(),
                    secuencia: $('#secuencia').val(),
                    retencion: $('#retencion').val(),
                }
            },
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                className: 'text-center',
                data: 'id_asiento_cabecera'
            }, {
                className: 'text-center',
                data: 'nro_comprobante'
            }, {
                className: 'text-center',
                data: 'numero'
            }, {
                data: 'razonsocial'
            }, {
                data: 'id'
            }, {
                data: 'descripcion'
            }, {
                className: 'text-right',
                data: 'valor_fuente',
            }, {
                className: 'text-right',
                data: 'porrenta',
            }, {
                className: 'text-right',
                data: 'valor_iva',
            }, {
                className: 'text-right',
                data: 'poriva',
                render: function (data, type, row) {
                    var poriva = row.poriva !== null ? row.poriva : 0;
                    return poriva;
                }
            }, {
                className: 'text-center',
                data: 'estado',
                render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }
            }, {
                className: 'text-center',
                data: 'usuariocrea'
            }, {
                className: 'text-center',
                data: 'usuariomod',
                render: function (data, type, row) {
                    $('#rfir').html(parseFloat(row.sumarenta).toFixed(2));
                    $('#rfiva').html(parseFloat(row.sumaiva).toFixed(2));
                    return row.usuariomod;
                }
            }],
        });
    }
}
function buscarDeudavsPagosClientes() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    else {
        $('#total_deuda').html('0.00');
        $('#total_abonos').html('0.00');
        $('#sumsaldo').html('0.00');
        $('#tablaDeudavsPagosClientes').dataTable().fnClearTable();
        $('#tablaDeudavsPagosClientes').dataTable().fnDestroy();
        $('#tablaDeudavsPagosClientes').DataTable({
            language: {
                "decimal": "",
                "emptyTable": $('#emptyTable').val(),
                "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
                "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
                "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
                "infoPostFix": "",
                "thousands": ",",
                "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
                "loadingRecords": $('#loadingRecords').val() + "...",
                "processing": $('#processing').val() + "...",
                "search": $('#search').val(),
                "zeroRecords": $('#zeroRecords').val(),
                "paginate": {
                    "first": $('#first').val(),
                    "last": $('#last').val(),
                    "next": $('#next').val(),
                    "previous": $('#previous').val()
                }
            },
            responsive: true,
            lengthChange: false,
            autoWidth: true,
            info: true,
            aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
            iDisplayLength: 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [{
                extend: 'copy',
                text: '<i class="fa fa-copy" title="Copy"></i>'
            },
            {
                extend: 'excel',
                title: tituloExcel,
                text: '<i class="fas fa-file-excel" title="Excel"></i>',
                footer: true,
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print" title="Print"></i>',
                customize: function (win) {
                    $(win.document.body).addClass('white-bg');
                    $(win.document.body).css('font-size', '10px');
                    $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
                },
                footer: true,
            }], "ajax": {

                url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/DeudasvsPagosClientesjs",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('#token').val(),
                },
                data: {
                    d: $('#desde').val(),
                    h: $('#hasta').val(),
                    cliente: $('#id_cliente').val(),
                }
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],  // en la columna 0 el check
                    sortable: false

                }
            ],
            columns: [{
                className: 'text-center',
                data: 'fecha'
            }, {
                data: 'detalle'
            }, {
                data: 'concepto'
            }, {
                className: 'text-center',
                data: 'id_asiento',
                render: function (data, type, row) {
                    return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\');">' + row.id_asiento + '</a>';
                }
            }, {
                data: 'nombre',
            }, {
                data: 'detalle_cuenta'
            }, {
                className: 'text-right',
                data: 'total_final',
                /*render: function (data, type, row) {
                    var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
                    return estado;
                }*/
            }, {
                className: 'text-right',
                data: 'deuda',

            }, {
                className: 'text-right',
                data: 'abono'
            }, {
                className: 'text-right',
                data: 'saldo',
                render: function (data, type, row) {
                    $('#total_deuda').html(parseFloat(row.total_deuda).toFixed(2));
                    $('#total_abonos').html(parseFloat(row.total_abonos).toFixed(2));
                    $("#sumsaldo").html(parseFloat(parseFloat(row.total_deuda).toFixed(2) - parseFloat(row.total_abonos).toFixed(2)).toFixed(2));
                    return row.saldo;
                }

            }], createdRow: function (row, data, dataIndex) {
                if (data.orden == 0) {
                    $(row).css('background-color', '#EAFAF1');
                } else {
                    $(row).css('background-color', '#F9EBEA');
                }
            },
        });
    }
}
function buscarDeudasvsPagosProveedores() {
    if ($("#desde").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de inicio...");
    } else if ($("#hasta").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de fin...");
    } else {
        $('#t_deuda').html('0.00');
        $('#t_abono').html('0.00');
        $('#t_saldo').html('0.00');
        $("#tablaDeudasvsPagosProveedores").dataTable().fnClearTable();
        $("#tablaDeudasvsPagosProveedores").dataTable().fnDestroy();
        $("#tablaDeudasvsPagosProveedores").dataTable({
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
            info: true,
            aLengthMenu: [
                [2, 4, 8, -1],
                [2, 4, 8, "All"],
            ],
            iDisplayLength: 1000,
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
                    footer: true,
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
                    "v3/" +
                    $("#controlador").val() + "/" + $("#submodulo_").val() + "/deudasvspagosproveedoresjs",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    d: $("#desde").val(),
                    h: $("#hasta").val(),
                    id_proveedor: $("#id_proveedor").val(),
                },
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6, 7, 8, 9],  // en la columna 0 el check
                    sortable: false
                }
            ],
            columns: [
                {
                    className: "text-center",
                    data: "fecha",
                    render: function (data, type, row) {
                        var fecha = row.fecha;
                        if (/COM-FA/.test(row.Comprobante)) {
                            fecha = row.fecha_documento;
                        }
                        return fecha;
                    }
                },
                {
                    className: "text-left",
                    data: "Comprobante",
                },
                {
                    data: "detalle",
                },
                {
                    className: 'text-center',
                    data: 'id_asiento',
                    render: function (data, type, row) {
                        return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\');">' + row.id_asiento + '</a>';
                    }
                },
                {
                    data: "razonsocial",
                },
                {
                    data: "cuenta",
                },
                {
                    className: "text-right",
                    data: "total_final",
                },
                {
                    className: "text-right",
                    data: "deuda",
                },
                {
                    className: "text-right",
                    data: "abono",
                },
                {
                    className: 'text-right',
                    data: 'saldo',
                    render: function (data, type, row) {
                        var saldo = row.deuda_total - row.abono_total;
                        $('#t_deuda').html(parseFloat(row.deuda_total).toFixed(2));
                        $('#t_abono').html(parseFloat(row.abono_total).toFixed(2));
                        $('#t_saldo').html(parseFloat(saldo).toFixed(2));
                        return row.saldo;
                    }
                }
            ],
            createdRow: function (row, data, dataIndex) {
                if (data.orden == 0) {
                    $(row).css('background-color', '#EAFAF1');
                } else {
                    $(row).css('background-color', '#F9EBEA');
                }
            }
        });
    }
}
function buscarDeudaPendientes() {
    if ($("#desde").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de inicio...");
    } else if ($("#hasta").val() == "") {
        return mensajes("warning", "Debe ingresar la fecha de final...");
    } else {
        $("#sumadeuda").html(0);
        $("#sumabono").html(0);
        $("#sumsaldo").html(0);
        $("#tablaDeudasPendientes").dataTable().fnClearTable();
        $("#tablaDeudasPendientes").dataTable().fnDestroy();
        $("#tablaDeudasPendientes").DataTable({
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
            lengthChange: false,
            autoWidth: true,
            info: true,
            aLengthMenu: [
                [2, 4, 8, -1],
                [2, 4, 8, "All"],
            ],
            iDisplayLength: 100,
            dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
            buttons: [
                {
                    extend: "copy",
                    text: '<i class="fa fa-copy" title="Copy"></i>',
                },
                {
                    extend: "excel",
                    title: tituloExcel,
                    text: '<i class="fas fa-file-excel" title="Excel"></i>',
                    footer: true,
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
                    footer: true,
                },
            ],
            ajax: {
                url: "v3/" + $("#controlador").val() + '/' + $("#submodulo").val() + "/DeudasPendientesjs",
                type: "post",
                headers: {
                    "X-CSRF-TOKEN": $("#token").val(),
                },
                data: {
                    d: $("#desde").val(),
                    h: $("#hasta").val(),
                    id_proveedor: $("#id_proveedor").val(),
                },
            },
            columnDefs: [
                {
                    orderable: false,
                    targets: [0, 1, 2, 3, 4, 5, 6],
                    sortable: false
                }
            ],
            columns: [
                {
                    className: "text-center",
                    data: "fecha_compra",
                },
                {
                    data: "comprobante",
                },
                {
                    data: "cuenta",
                },
                {
                    data: "detalle",
                },
                {
                    data: "id_asiento",
                },
                {
                    data: "razonsocial",
                },

                {
                    className: "text-right",
                    data: "valor_contable",
                },
                {
                    className: "text-right",
                    data: "deuda",
                },
                {
                    className: "text-right",
                    data: "abono",
                },
                {
                    className: "text-right",
                    data: "saldo",
                    render: function (data, type, row) {
                        $("#sumadeuda").html(parseFloat(row.total_deudas).toFixed(2));
                        $("#sumabono").html(parseFloat(row.total_abonos).toFixed(2));
                        $("#sumsaldo").html(parseFloat(parseFloat(row.total_deudas).toFixed(2) - parseFloat(row.total_abonos).toFixed(2)).toFixed(2));
                        return row.saldo;
                    }
                },

            ],
            createdRow: function (row, data, dataIndex) {
                if (data.orden == 0) {
                    $(row).css('background-color', '#EAFAF1');
                } else {
                    $(row).css('background-color', '#F9EBEA');
                }
            }
        });
    }
}



function buscarAsientos() {
    if ($('#desde').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de inicio...');
    } else if ($('#hasta').val() == '') {
        return mensajes('warning', 'Debe ingresar la fecha de final...');
    }
    $('#tableLibroDiario').dataTable().fnClearTable();
    $('#tableLibroDiario').dataTable().fnDestroy();
    $('#tableLibroDiario').DataTable({
        language: {
            "decimal": "",
            "emptyTable": $('#emptyTable').val(),
            "info": $('#mostrando').val() + " _START_ " + $('#a').val() + " _END_ " + $('#de').val() + " _TOTAL_ " + $('#entradas').val(),
            "infoEmpty": $('#mostrando').val() + " 0 " + $('#a').val() + " 0 " + $('#de').val() + " 0 " + $('#entradas').val(),
            "infoFiltered": "(" + $('#filtrado').val() + " " + $('#de').val() + " _MAX_ total " + $('#entradas').val() + ")",
            "infoPostFix": "",
            "thousands": ",",
            "lengthMenu": $('#mostrar').val() + " _MENU_ " + $('#entradas').val(),
            "loadingRecords": $('#loadingRecords').val() + "...",
            "processing": $('#processing').val() + "...",
            "search": $('#search').val(),
            "zeroRecords": $('#zeroRecords').val(),
            "paginate": {
                "first": $('#first').val(),
                "last": $('#last').val(),
                "next": $('#next').val(),
                "previous": $('#previous').val()
            }
        },
        responsive: true,
        lengthChange: false,
        autoWidth: true,
        info: true,
        aLengthMenu: [[2, 4, 8, -1], [2, 4, 8, "All"]],
        iDisplayLength: 100,
        dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
        columnDefs: [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 5, 6] },
        ],
        buttons: [{
            extend: 'copy',
            text: '<i class="fa fa-copy" title="Copy"></i>'
        },
        {
            extend: 'excel',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-excel" title="Excel"></i>',
            footer: true,
        },
        {
            extend: 'pdf',
            title: 'Libro diario | Empresa: ' + $('#nombreEmpresa').val() + ' | Desde: ' + $('#desde').val() + ' al ' + $('#hasta').val(),
            text: '<i class="fas fa-file-pdf" title="PDF"></i>'
        }, {
            extend: 'print',
            text: '<i class="fa fa-print" title="Print"></i>',
            customize: function (win) {
                $(win.document.body).addClass('white-bg');
                $(win.document.body).css('font-size', '10px');
                $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
            }
        }], "ajax": {
            url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "/detalleLibroDiario",
            type: 'post',
            headers: {
                'X-CSRF-TOKEN': $('#token').val(),
            },
            data: {
                d: $('#desde').val(),
                h: $('#hasta').val(),
            },
        },
        columns: [{
            data: 'id',
            render: function (data, type, row) {
                return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id + '\');">' + row.id + '</a>';
            }
        }, {
            className: 'text-center',
            data: 'fecha_asiento',
            render: function (data, type, row) {
                return getfecha(row.fecha_asiento);
            }
        }, {
            className: 'text-center',
            data: 'plan',
        }, {
            data: 'nombre_cuenta'
        }, {
            className: 'text-right',
            data: 'debe',
            render: function (data, type, row) {
                return row.debe;
            }
        }, {
            className: 'text-right',
            data: 'haber',
            render: function (data, type, row) {
                $('#totalDebe').html(row.total_debe);
                $('#totalHaber').html(row.total_haber);
                return row.haber;
            }
        }, {
            data: 'observacion'
        }, {
            className: 'text-center',
            data: 'usuariocrea'
        }, {
            className: 'text-center',
            data: 'usuariomod',
        }],
    });
}