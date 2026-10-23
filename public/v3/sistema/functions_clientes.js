var fila = 0;
var filaCruce = 0;
var filaCruceCiv = 0;
var filaCruceCivIds = [];
var aux_filaCruceCiv = 0;
var filaCompIngreso = 0;
var filaCompIngresoVarios = 0;
var aux_filaCompIngresoVarios = 0;
var filaCompIngresoIds = [];
var filaImpuesto = 0;
$(document).ready(function () {

  $("input[data-bootstrap-switch]").each(function () {
    $(this).bootstrapSwitch("state", $(this).prop("checked"));
  });


  $("#js-data-nombre-cliente")
    .select2({
      minimumInputLength: 3,
      cache: true,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getnombrecliente",
        delay: 250,
        data: function (params) {
          return {
            term: params.term,
          };
        },
        processResults: function (data) {
          return {
            results: $.map(data, function (item) {
              return {
                text: item.nombre,
                id: item.id,
              };
            }),
          };
        },
      },
      language: {
        inputTooShort: function () {
          return "Por favor ingrese 3 caracteres....";
        },
      },
    }).on("select2:select", function (e) {
      let selectedOption = e.params.data;
      cargarFacturas(selectedOption.text);
    });


  $("#clientef").select2({
    minimumInputLength: 3,
    cache: true,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarClienteF",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (data) {
        console.log(data);
        return {
          results: $.map(data, function (item) {
            return {
              text: item.nombre,
              id: item.id,
            };
          }),
        };
      },
    },
    language: {
      inputTooShort: function () {
        return "Por favor ingrese 3 caracteres....";
      },
    },
  });
  if ($("#submodulo_").val() == "nuevaretencion") {
    $(".botonMenu").trigger("click");
  }
  if ($('#submodulo_').val() == 'listadeclientes') {
    $("#elementTour").val("hero-contenido,hero-accion,hero-agregar,hero-listretenciones");
  } else if ($("#submodulo_").val() == "retenciones") {
    $("#elementTour").val("hero-listretenciones");
  }
  try {
    $("#tableClientes").DataTable({
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
      iDisplayLength: 50,
      processing: true,
      serverSide: true,
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        error(error) {
          console.log(error)
        },
      },
      columns: [
        {
          data: "identificacion",
        },
        {
          data: "nombre",
        },
        {
          data: "direccion_representante",
          render: function (data, type, row) {
            var direccion = row.direccion === null ? row.direccion_representante : row.direccion;
            return direccion;
          }
        },
        {
          data: "email_representante",
        },
        {
          data: "estado",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "identificacion",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCliente(\'' + row.identificacion + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.identificacion + "','" + row.estado + "','Cliente');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableCruceCuentas").DataTable({
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
      iDisplayLength: 50,
      order: [[0, 'desc']],
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
        error() {
          // window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id",
        },
        {
          className: 'text-center',
          data: 'id_asiento_cabecera',
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
          }
        },
        {
          data: "id_cliente",
        },
        {
          data: "nombre",
        },
        {
          data: "nro_comprobante",
        },
        {
          data: "detalle",
        },
        {
          className: "text-right",
          data: "total",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var cadena = row.ucnombre;
            var letra = cadena.charAt(0);
            return letra + ". " + row.ucapellido;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#anulado').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCruce(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div>';
            if (row.estado == 1) {
              botones += '<a class="dropdown-item" href="javascript:;" onclick="anularCruce(\'' + row.id + "');\">" + accion + "</a>";
            } else {
              botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
            }
            botones += "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableCruceValores").DataTable({
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
      order: [[0, 'desc']],
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
        error() {
          //window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id",
        },
        {
          data: "fecha",
        },
        {
          className: 'text-center',
          data: 'id_asiento_cabecera',
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
          }
        },
        {
          data: "id_cliente",
        },
        {
          data: "nombre",
        },
        {
          data: "detalle",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var cadena = row.ucnombre;
            var letra = cadena.charAt(0);
            return letra + ". " + row.ucapellido;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#anulado').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCruceValores(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div>';
            if (row.estado == 1) {
              botones += '<a class="dropdown-item" href="javascript:;" onclick="anularCruceValores(\'' + row.id + "');\">" + accion + "</a>";
            } else {
              botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
            }
            botones += "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableCompIngreso").DataTable({
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
      pageLength: 50,
      responsive: true,
      lengthChange: true,
      autoWidth: true,
      order: [[3, 'desc']],
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
        error() {
          //window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id",
        },
        {
          data: "secuencia",
        },
        {
          className: 'text-center',
          data: 'id_asiento_cabecera',
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
          }
        },
        {
          className: 'text-center',
          data: "fecha",
        },
        {
          data: "nombre",
        },
        {
          data: "observaciones",
        },
        {
          className: 'text-right',
          data: "total_ingreso",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var cadena = row.ucnombre;
            var letra = cadena.charAt(0);
            return letra + ". " + row.ucapellido;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#anulado').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:red;"></i> Anulado';
            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCompIngreso(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a>';
            botones += "<a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFCompIngreso('" + row.id + "');\"><i class='fas fa-file-pdf' style='color:orange;'></i> PDF</a>";
            botones += "<div class='dropdown-divider'></div>"
            if (row.estado == 1) {
              botones += '<a class="dropdown-item" href="javascript:;" onclick="AnularCompIngreso(\'' + row.id + "');\">" + accion + "</a>";
            } else {
              botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
            }
            botones += "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableCompIngresoVarios").DataTable({
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
      pageLength: 50,
      responsive: true,
      lengthChange: true,
      autoWidth: true,
      order: [[0, 'desc']],
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
        error() {
          //window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id",
        },
        {
          data: "secuencia",
        },
        {
          className: 'text-center',
          data: 'id_asiento_cabecera',
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
          }
        },
        {
          className: 'text-center',
          data: "fecha",
        },
        {
          data: "nombre_caja",
        },
        {
          data: "concepto",
        },
        {
          className: 'text-right',
          data: "total_ingreso",
        },
        {
          className: 'text-center',
          data: "usuariocrea",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#anulado').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:red;"></i> Anulado';
            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCompIngresoVarios(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a>';
            botones += "<a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFCompIngresoVarios('" + row.id + "');\"><i class='fas fa-file-pdf' style='color:orange;'></i> PDF</a>";
            botones += "<div class='dropdown-divider'></div>"
            if (row.estado == 1) {
              botones += '<a class="dropdown-item" href="javascript:;" onclick="AnularCompIngresoVarios(\'' + row.id + "');\">" + accion + "</a>";
            } else {
              botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
            }
            botones += "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableRetenciones").DataTable({
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
      order: [[0, 'desc']],
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
        data: {
          d: $("#desde").val(),
          h: $("#hasta").val()
        },
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        error(error) {
          console.log(error);
        },
      },
      columns: [
        {
          className: "text-center",
          data: "id",
        },
        {
          className: "text-center",
          data: "fecha",
        },
        {
          className: "text-center",
          data: "id_asiento_cabecera",
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
          }
        },
        {
          data: "cliente",
          render: function (data, type, row) {
            return row.id_cliente + ' | ' + row.cliente;
          }
        },
        {
          data: "descripcion",
        },
        {
          className: "text-center",
          data: "id_venta",
        },
        {
          className: "text-center",
          data: "nro_comprobante",
        },
        {
          className: "text-right",
          data: "valor_fuente",
        },
        {
          className: "text-right",
          data: "valor_iva",
        },
        {
          className: "text-right",
          data: "total",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var cadena = row.ucnombre;
            var letra = cadena.charAt(0);
            return letra + ". " + row.ucapellido;
          },
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
            if (row.estado == 0) {
              estado = '<label class="text-danger">' + $('#anulado').val() + '</label>';
            }
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:red;"></i> Anulado';
            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' + row.id + "','" + row.nro_comprobante + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + ' </a><div class="dropdown-divider"></div>';
            if (row.estado == 1) {
              botones += '<a class="dropdown-item" href="javascript:;" onclick="AnularClienteRetencion(\'' + row.id + "');\">" + accion + "</a>";
            } else {
              botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
            }
            return botones;
          },
        },
      ],
    });
    $("#tableRubros").DataTable({
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
          className: "text-center",
          data: "codigo",
        },
        {
          data: "nombre",
        },
        {
          data: "debe",
        },
        {
          data: "haber",
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado =
              row.estado == 1
                ? '<label class="text-success">' +
                $("#activo").val() +
                "</label>"
                : '<label class="text-danger">' +
                $("#inactivo").val() +
                "</label>";
            return estado;
          },
        },
        {
          className: "text-center",
          data: "codigo",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> ' +
                $("#desactivar").val();
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRubros(\'' + row.codigo + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.codigo + "','" + row.estado + "','Rubros');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableNotadecredito").DataTable({
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
      processing: true,
      serverSide: true,
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
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
        { data: "id", },
      ],
    });
  } catch (error) {
    console.log(error);
  }
  $("#tipo").change(function () {
    if ($("#tipo").val() != "" && $("#tipo").val() == 4) {
      $("#divOculto").show();
    } else {
      $("#divOculto").hide();
    }
  });
  $("#impuesto").change(function () {
    var e = parseFloat($("#impuesto").val());
    if (e == "" || isNaN(e) || e <= 0) {
      e = 0;
    }
    $("#impuesto").val(parseFloat(e).toFixed(2, 2));
  });
  $("#tipo").on("change", function () {
    let inputElement = $(this);
    gestionarCambio(inputElement);
  });
  $("#btnGuardarCliente").on("click", function () {
    if ($("#tipo").val() == "") {
      return mensajes("warning", "El campo identificación esta vacio ...");
    } else if ($("#nombre").val() == "") {
      return mensajes("warning", "El campo nombre esta vacio ...");
    } else if ($("#identificacion").val() == "") {
      return mensajes("warning", "El campo identificacion esta vacio ...");
    } else if ($("#clase").val() == "" || $("#clase").val() === null) {
      return mensajes("warning", "El campo clase esta vacio ...");
    } else if ($('#tipo').val() == 4 && $("#nombre_representante").val() == "") {
      return mensajes("warning", "El campo nombre representante esta vacio ...");
    } else if ($('#tipo').val() == 4 && $("#cedula").val() == "") {
      return mensajes("warning", "El campo cedula del representante esta vacio ...");
    } else if ($("#pais").val() == "") {
      return mensajes("warning", "El campo pais esta vacio ...");
    } else if ($("#provincia").val() == "") {
      return mensajes("warning", "El campo provincia esta vacio ...");
    } else if ($("#ciudad").val() == "") {
      return mensajes("warning", "El campo ciudad esta vacio ...");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#debe").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarCuenta2",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        return {
          results: $.map(response, function (categoria) {
            return { id: categoria.plan, text: categoria.text };
          }),
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $("#haber").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarCuenta2",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        return {
          results: $.map(response, function (categoria) {
            return { id: categoria.plan, text: categoria.text };
          }),
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $("#serie").on("keydown", function (event) {
    var serie = $('#serie').val();
    var keyCode = event.keyCode;
    if (keyCode != 8) {
      if (serie.length == 3) {
        $('#serie').val(serie + '-');
      }
    }
  });
  $('#numero').change(function () {
    var originalString = $('#numero').val();
    var paddedString = PadLeft(originalString, 9);
    $('#numero').val(paddedString);
  });
  $("#serie_retencion").on("keydown", function (event) {
    var serie_retencion = $('#serie_retencion').val();
    var keyCode = event.keyCode;
    if (keyCode != 8) {
      if (serie_retencion.length == 3) {
        $('#serie_retencion').val(serie_retencion + '-');
      }
    }
  });
  $('#secuencial_retencion').change(function () {
    var originalString = $('#secuencial_retencion').val();
    var paddedString = PadLeft(originalString, 9);
    $('#secuencial_retencion').val(paddedString);
  });
  $("#serie_retencion").blur(function () {
    if ($('#serie_retencion').val().length != 7) {
      return mensajes("warning", "Faltan dígitos en la serie");
    }
  });
  $("#autorizacion_retencion").blur(function () {
    if ($('#autorizacion_retencion').val().length != 10 && $('#autorizacion_retencion').val().length != 49) {
      return mensajes("warning", "Ingrese al menos 10 dígitos y máximo 49 para la número de autorización");
    }
  });
  $("#btnGuardarRubros").click(function () {
    if ($("#codigo").val() == "") {
      mensajes("warning", "Ingrese el codigo del rubro");
    } else if ($("#nombre").val() == "") {
      mensajes("warning", "Ingrese el nombre del rubro");
    } else if ($("#debe").val() == "") {
      mensajes("warning", "Seleccione la cuenta debe del rubro");
    } else if ($("#haber").val() == "") {
      mensajes("warning", "Seleccione la cuenta haber del rubro");
    } else if ($("#tipo_r").val() == "") {
      mensajes("warning", "Seleccione el tipo del rubro");
    } else {
      if ($("#codigo").val() != "") {
        validarCodigo();
      }
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#comprobante").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarFactVenta",
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
        //window.open('v3/admin', '_self');
      },
      cache: true,
    },
  });
  $("#comprobante").change(function () {
    $.ajax({
      url: "v3/" + $("#controlador").val() + "/buscarValor",
      type: "get",
      data: {
        id: $(this).val(),
      },
      dataType: "json",
      success: function (json) {
        if (json != "") {
          $("#info").val(json.valor_contable);
          $("#fecha_fact").val(json.fecha);
          $("#clientecc").val(json.identificacion != null ? json.identificacion + ' | ' + json.nombre : '');

        }
      },
    });
  });
  $("#impuesto").change(function () {
    var subtotal = parseFloat($("#subtotal").val());
    var total = parseFloat($("#total").val());
    var imp = parseFloat($("#impuesto").val());
    if (imp == "" || isNaN(imp) || imp <= 0) {
      imp = 0;
    }
    if (imp > $("#info").val() || (subtotal + imp) > $("#info").val()) {
      $("#impuesto").val(parseFloat(0).toFixed(2, 2));
      $("#total").val(parseFloat(subtotal).toFixed(2, 2));
      return mensajes("warning", "El total no dede ser mayor al valor de la factura");
    } else {
      $('#total').val(parseFloat(parseFloat(imp) + parseFloat(subtotal)).toFixed(2));
    }
  });
  $('#btnguardarRetencion').click(function () {
    let validacion = $("#tr_" + filaImpuesto).find('td').length > 0;
    if ($('#facturas').val() == '') {
      return mensajes('warning', 'Debe seleccionar una factura');
    } else if ($('#serie_retencion').val() == '') {
      return mensajes('warning', 'Por favor ingrese la serie de la retención');
    } else if ($('#secuencial_retencion').val() == '') {
      return mensajes('warning', 'Por favor ingrese el secuencial de la retención');
    } else if ($('#autorizacion_retencion').val() == '') {
      return mensajes('warning', 'Por favor ingrese el numero de autorización de la retención');
    } else if ($('#descripcion').val() == '') {
      return mensajes('warning', 'Debe ingresar un concepto para la retención');
    } else if (!validacion) {
      return mensajes('warning', 'Debe agregar una fila de impuestos');
    } else if ($("#tipo_retencion1" + filaImpuesto).val() === '' || $("#porcentaje_retencion" + filaImpuesto).val() === '') {
      return mensajes('warning', 'Debe agregar el tipo impuesto y procentaje');
    } else {
      mensajes('question', $('#pregunta').val(), 'frm_', 'action', $('#base_url').val() + '/v3/clientes/retenciones');
    }
  });
  $("#btnguardarCruce").click(function () {
    var regex = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|1\d|2\d|3[01])$/;
    if (!regex.test($("#fecha").val())) {
      return mensajes("warning", "Ingrese una fecha valida");
    } else if ($("#comprobante").val() == "") {
      return mensajes("warning", "Seleccione la factura para continuar");
    } else if ($("#concepto").val() == "") {
      return mensajes("warning", "Ingrese el concepto del cruce de cuenta");
    } else if ($("#sucursal").val() == "") {
      return mensajes("warning", "Seleccione el establecimiento");
    } else if (filaCruce <= 0) {
      return mensajes("warning", "Agregue el detalle de cuentas");
    }
    else {
      if (filaCruce > 0) {
        var validacionCuenta = false;
        var validacionDetalle = false;
        var validacionValores = false;

        $("select[name='cuenta[]']").each((i, v) => {
          if (v.value == "") {
            validacionCuenta = true;
          }
          if ($("#detalle_" + i).val() === "") {
            validacionDetalle = true;
          }
          if ($("#valor_" + i).val() === "0.00") {
            validacionValores = true;
          }
        });
        if (validacionCuenta) {
          return mensajes("warning", "Debe ingresar la cuenta");
        }
        if (validacionDetalle) {
          return mensajes("warning", "Debe ingresar el detalle");
        }
        if (validacionValores) {
          return mensajes("warning", "El valor no puede ser igual a 0");
        }
      }
      mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/clientes/crucedecuentas');
    }
  });
  $("#id_cliente").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarCliente",
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
        //window.open('v3/admin', '_self');
      },
      cache: true,
    },
  });
  $("#id_vendedor").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarVendedor",
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
        //window.open('v3/admin', '_self');
      },
      cache: true,
    },
  });
  $("#agregarlineaValoresRecibidos").click(function () {
    if ($("#id_cliente").val() == "") {
      return mensajes("warning", "Seleccione un cliente para continuar");
    } else {
      var cliente_datos = $('#id_cliente').val();
      var clienteArray_datos = cliente_datos.split('|');
      var bancosCombo = "";
      var tipoPagoCombo = "";
      var tipoTarjetaCombo = "";
      var now = new Date();
      var day = ("0" + now.getDate()).slice(-2);
      var month = ("0" + (now.getMonth() + 1)).slice(-2);
      var today = now.getFullYear() + "-" + month + "-" + day;
      var json = ajax("v3/" + $("#controlador").val() + "/getMetodoPagoFactura", null, 'json');
      if (json.responseJSON != '') {
        json = json.responseJSON;
        $.each(json.tipoPago, function (i, item) {
          tipoPagoCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
        });
        $.each(json.bancos, function (i, item) {
          bancosCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
        });
        $.each(json.tipoTarjeta, function (i, item) {
          tipoTarjetaCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
        });
        var html_ = '<tr style="background-color: white" id="trPago_' + filaCompIngreso + '">';
        html_ += '<td style="min-width: 11%;"><select class="form-control select2_" style="width:100%;" id="id_pago_' + filaCompIngreso + '" onchange="asignarPago(\'' + filaCompIngreso + '\')" name="id_pago[]"><option value="">--Seleccione--</option>' + tipoPagoCombo + "</select></td>";
        html_ += '<td style="min-width: 8%;"><input type="date" class="form-control" id="fecha_pago_' + filaCompIngreso + '" name="fecha_pago[]" value="' + today + '" /></td>';
        html_ += '<td style="min-width: 10%;"><input type="text" class="form-control" id="numero_' + filaCompIngreso + '" name="numero[]" value="" /></td>';
        html_ += '<td style="min-width: 15%;"><select class="form-control select2_" style="width:100%;" id="id_bancos_' + filaCompIngreso + '" name="id_bancos[]"><option value="">--Seleccione--</option>' + bancosCombo + "</select></td>";
        html_ += '<td style="min-width: 14%;"><select class="form-control select2_" style="width:100%;" id="id_tarjetas_' + filaCompIngreso + '" name="id_tarjetas[]"><option value="">--Seleccione--</option>' + tipoTarjetaCombo + "</select></td>";
        html_ += '<td style="min-width: 10%;"><input type="text" class="form-control" id="cuenta_' + filaCompIngreso + '" name="cuenta[]" value="" /></td>';
        html_ += '<td style="min-width: 13%;"><input type="text" class="form-control" id="giradoA_' + filaCompIngreso + '" name="giradoA[]" value="' + clienteArray_datos[5] + '" /></td>';
        html_ += '<td style="min-width: 10%;"><input type="text" class="form-control text-right" id="valor' + filaCompIngreso + '" name="valor[]" value="0.00" onchange="valor_vr(' + filaCompIngreso + ')" /></td>';
        html_ += '<td style="min-width: 9%;" align="center"><a href="javascript:;" class="btn btn-danger" onclick="PagoEliminar(\'' + filaCompIngreso + '\');"><i class="fa fa-trash-alt"></i></a></td>';
        html_ += "</tr>";
        $("#detalleValoresRecibidos").append(html_);
        $(".select2_").select2();
        filaCompIngreso++;
      }
    }
  });
  $("#agregarlineaValoresRecibidosciv").click(function () {
    var bancosCombo = "";
    var tipoPagoCombo = "";
    var tipoTarjetaCombo = "";
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + month + "-" + day;
    var json = ajax("v3/" + $("#controlador").val() + "/getMetodoPagoFactura", null, 'json');
    if (json.responseJSON != '') {
      json = json.responseJSON;
      $.each(json.tipoPago, function (i, item) {
        tipoPagoCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
      });
      $.each(json.bancos, function (i, item) {
        bancosCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
      });
      $.each(json.tipoTarjeta, function (i, item) {
        tipoTarjetaCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
      });
      var uniqueId = filaCompIngresoVarios;
      filaCompIngresoIds.push(uniqueId);

      var html_ = '<tr style="background-color: white" id="trPago_' + uniqueId + '">';
      html_ += '<td style="min-width: 11%;"><select class="form-control select2_" style="width:100%;" id="id_pago_' + uniqueId + '" onchange="asignarPago(\'' + uniqueId + '\')" name="id_pago[]"><option value="">--Seleccione--</option>' + tipoPagoCombo + "</select></td>";
      html_ += '<td style="min-width: 8%;"><input type="date" class="form-control" id="fecha_pago_' + uniqueId + '" name="fecha_pago[]" value="' + today + '" /></td>';
      html_ += '<td style="min-width: 10%;"><input type="text" class="form-control" id="numero_' + uniqueId + '" name="numero[]" value="" /></td>';
      html_ += '<td style="min-width: 15%;"><select class="form-control select2_" style="width:100%;" id="id_bancos_' + uniqueId + '" name="id_bancos[]"><option value="">--Seleccione--</option>' + bancosCombo + "</select></td>";
      html_ += '<td style="min-width: 14%;"><select class="form-control select2_" style="width:100%;" id="id_tarjetas_' + uniqueId + '" name="id_tarjetas[]"><option value="">--Seleccione--</option>' + tipoTarjetaCombo + "</select></td>";
      html_ += '<td style="min-width: 10%;"><input type="text" class="form-control" id="cuenta_' + uniqueId + '" name="cuenta[]" value="" /></td>';
      html_ += '<td style="min-width: 13%;"><input type="text" class="form-control" id="giradoA_' + uniqueId + '" name="giradoA[]" value="" /></td>';
      html_ += '<td style="min-width: 10%;"><input type="text" class="form-control text-right" id="valor' + uniqueId + '" name="valor[]" value="0.00" onchange="valor_civ(' + uniqueId + ')" /></td>';
      html_ += '<td style="min-width: 9%;" align="center"><a href="javascript:;" class="btn btn-danger" onclick="PagoEliminarciv(\'' + uniqueId + '\');"><i class="fa fa-trash-alt"></i></a></td>';
      html_ += "</tr>";
      $("#detalleValoresRecibidosciv").append(html_);
      $(".select2_").select2();
      filaCompIngresoVarios++;
      aux_filaCompIngresoVarios++;

    }
  });
  $("#btnguardarCompIngreso").click(function () {
    var total = parseFloat($("#total").val());
    var valor_total = parseFloat($("#valor_total").val());
    var total_suma = parseFloat($("#total_suma").val());
    var superavit = parseInt($("#verificar_superavit").val());
    var sumas_abono = parseFloat($("#total_suma_a").val());
    var regex = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|1\d|2\d|3[01])$/;
    if (!regex.test($("#fecha").val())) {
      return mensajes("warning", "Ingrese una fecha valida");
    } else if ($("#id_cliente").val() == "") {
      return mensajes("warning", "Seleccione un cliente para continuar");
    } else if ($("#valor_total").val() <= 0) {
      return mensajes("warning", "Ingrese el valor");
    } else if ($("#sucursal").val() == "") {
      return mensajes("warning", "Seleccione el establecimiento");
    } else if (filaCompIngreso <= 0) {
      return mensajes("warning", "Agregue el detalle de valores recibidos");
    } else if (valor_total != total) {
      return mensajes("warning", "Los valores recibidos, deben ser igual al total de ingresos");
    } else {
      if (filaCompIngreso > 0) {
        let validacionTipoPago = false;
        let validacionValor = false;

        $("select[name='id_pago[]']").each((i, v) => {
          if (v.value == "") {
            validacionTipoPago = true;
          }
          if ($("#valor" + i).val() == 0) {
            validacionValor = true;
          }
        });
        if (validacionTipoPago) {
          return mensajes("warning", "Debe ingresar el tipo de pago");
        }
        if (validacionValor) {
          return mensajes("warning", "Debe ingresar el valor");
        }
      }
    }
    if (superavit == 1) {
      if (sumas_abono < total_suma) {
        var resta_superavit = parseFloat(total_suma - sumas_abono);
        var resta_fixed = resta_superavit.toFixed(2, 2);
        Swal.fire({
          title: 'Existe un superávit de ' + resta_fixed + ' en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?',
          text: $('#informa').val(),
          icon: 'info',
          showCancelButton: true,
          cancelButtonText: 'No',
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Si'
        }).then((result) => {
          if (result.isConfirmed) {
            $("#total_favor").val(resta_superavit);
            mensajes("question", $("#pregunta").val(), 'frm_', 'action1', $('#base_url').val() + '/v3/clientes/nuevo_compIngreso');
          }
        });
      } else {
        mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/clientes/nuevo_compIngreso');
      }
    } else if (superavit == 0) {
      $("#total_favor").val(total);
      mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/clientes/nuevo_compIngreso');
    } else {
      return mensajes("error", "Error");
    }
  });
  $("#btnguardarCruceValores").click(function () {
    var total_anticipos = parseFloat($("#total_anticipos").val());
    var total_suma_cv = parseFloat($("#total_suma_cv").val());
    var regex = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|1\d|2\d|3[01])$/;
    if (!regex.test($("#fecha").val())) {
      return mensajes("warning", "Ingrese una fecha valida");
    } else if ($("#id_cliente").val() == "") {
      return mensajes("warning", "Seleccione un cliente para continuar");
    } else if ($("#concepto").val() == "") {
      return mensajes("warning", "Ingrese la observacion");
    } else if ($("#sucursal").val() == "") {
      return mensajes("warning", "Seleccione el establecimiento");
    } else if (total_anticipos <= 0) {
      return mensajes("warning", "Agregue el abono de los valores a favor");
    } else if (total_suma_cv <= 0 || isNaN(total_suma_cv)) {
      return mensajes("warning", "Use los valores recibidos para pagar la deuda");
    } else {
      mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/clientes/nuevo_cruce_valores');
    }
  });
});
$(document).on('select2:open', function (e) {
  if (e.target.id !== "") {
    document.querySelector(`[aria-controls="select2-${e.target.id}-results"]`).focus();
  }
});
function agregarNotadecredito() {
  window.open("v3/" + $("#controlador").val() + "/nuevanotadecredito", "_self");
}
function agregarCliente(id) {
  limpiarModal();
  $("#identificacion").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal_cliente").html('<i class="fas fa-plus-square"></i> ' + $('#nuevo').val() + ' Cliente');
  $("#myModal").modal("toggle");
}
function agregarRubros() {
  $('#emily').val('nuevo');
  limpiarModal();
  $("#codigo").removeAttr("readonly");
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i> ' + $("#nuevo").val() + " " + $("#submodulo_").val());
  $("#myModal").modal("toggle");
}
function verCliente(id) {
  limpiarModal();
  $("#titulo_modal_cliente").html('<i class="fas fa-edit"></i> Editar Cliente');
  var data = {
    id: id
  };
  var json = ajax('v3/' + $('#controlador').val() + '/getCliente', data, 'json');
  if (json.responseJSON != null) {
    json = json.responseJSON;
    $("#nombre").val(json.nombre);
    idPais = json.pais;
    idProvincia = json.provincia;
    idCiudad = json.id_ciudad;
    $("#pais").val(idPais).trigger("change");
    $("#tipo_identificacion").val(json.tipo).trigger("change");
    $("#clase").val(json.clase).trigger("change");
    $("#identificacion").val(json.identificacion);
    $("#email_representante").val(json.email_representante);
    $('#telefono1_representante').val(json.telefono1_representante);
    $('#telefono2_representante').val(json.telefono2_representante);
    // representante
    $("#nombre_representante").val(json.nombre_representante);
    $("#cedula").val(json.cedula_representante);
    $("#pais_representante").val(json.pais_representante).trigger("change");
    $("#provincia_representante")
      .val(json.provincia_representante)
      .trigger("change");
    $("#ciudad_representante")
      .val(json.ciudad_representante)
      .trigger("change");
    // ubicacion
    var direccion = json.direccion_representante !== null ? json.direccion_representante : json.direccion;
    $("#direccion").val(direccion);
    $("#urbanizacion").val(json.urbanizacion);
    $("#etapa").val(json.etapa);
    $("#manzana").val(json.manzana);
    $("#villa").val(json.villa);
    $("#referencia").val(json.referencia);
    if (json.estado == 1) {
      $("#cheestado").attr("checked", true);
      $("#estado").val(1);
    } else {
      $("#cheestado").attr("checked", false);
      $("#estado").val(0);
    }
    $("#myModal").modal("toggle");
  }
}
function verCruce(id_cruce) {
  limpiarModal();
  var tablaHtml = '';
  $("#titulo_modal").html('<i class="fas fa-eye"></i> Visualizador Cruce de Cuentas');
  var data = {
    id: id_cruce,
  };
  var json = ajax('v3/' + $('#controlador').val() + '/getCruceCuentas', data, 'json');
  if (json.responseJSON != null) {
    json = json.responseJSON;
    $("#id").val(json.id);
    $("#numero").val(json.secuencia);
    $("#asiento").val(json.id_asiento_cabecera);
    $("#fecha").val(json.fecha);
    $("#fecha_fact").val(json.fecha_fact);
    $("#concepto").val(json.detalle);
    $("#info").val(json.total);
    $("#comprobanteCrucec").val('Fact #: ' + json.nro_comprobante + ' | ' + json.nombre + ' | Id: ' + json.id_fact);
    $("#clientecc").val(json.cliente);
    $("#tablaDetalles").empty();
    var dataCruce = {
      id: id_cruce,
    };
    var cruce = ajax('v3/' + $('#controlador').val() + '/getDetalleCruceCuentas', dataCruce, 'json');
    if (cruce.responseJSON != null) {
      var detalleJson = cruce.responseJSON;
      var total = 0;
      if (detalleJson != null && detalleJson.length > 0) {
        tablaHtml = '<tr>';
        for (var i = 0; i < detalleJson.length; i++) {
          tablaHtml += '<td style="width: 30%;">' + detalleJson[i].codigo + ' | ' + detalleJson[i].nombre + '</td><td style="width: 40%;">' + detalleJson[i].observaciones + '</td><td style="width: 10%;"> USD </td><td style="width: 10%; text-align: right;">' + detalleJson[i].total + '</td><td style="width: 10%; text-align: right;">' + detalleJson[i].total + '</td></tr>';
          total += parseFloat(detalleJson[i].total) || 0;
        }
        tablaHtml += '</tbody></table>';
        $("#total").val(total.toFixed(2, 2));
        $("#tablaDetalles").html(tablaHtml);
      }
    }
    $("#myModal").modal("toggle");
  }
}
function verRubros(codigo) {
  $('#emily').val('editar');
  var data = {
    codigo: codigo,
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getConfiguracionRubros", data, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    $("#codigo").val(json.codigo);
    $("#nombre").val(json.nombre);
    var debe = {
      id: json.debe,
    };
    var html = ajax("v3/" + $("#controlador").val() + "/traerCuentaGeneral", debe, 'html');
    if (html.responseText != '') {
      $("#debe").append(html.responseText).trigger("change");
    }
    var haber = {
      id: json.haber,
    };
    var html_ = ajax("v3/" + $("#controlador").val() + "/traerCuentaGeneral", haber, 'html');
    if (html_.responseText != '') {
      $("#haber").append(html.responseText).trigger("change");
      $("#tipo_r").val(json.tipo).trigger("change");
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      $("#codigo").attr("readonly", "readonly");
      $("#titulo_modal").html('<i class="fas fa-plus-square"></i> Modificar Rubro');
      $("#myModal").modal("toggle");
    }
  }
}
$("#googleMaps").on("click", function () {
  let pais = $(".pais option:selected").text();
  let provincia = $(".provincia option:selected").text();
  let ciudad = $("ciudad option:selected").text();
  let urbanizacion = $("#urbanizacion").val();
  let etapa = $(".etapa").val();
  let mz = $(".manzana").val();
  let villa = $(".villa").val();
  let direccion = $(".direccion").val();
  if (pais || provincia || ciudad || urbanizacion || etapa || mz || direccion) {
    let direccionCompleta = [pais, provincia, ciudad, urbanizacion, etapa, mz, villa, direccion].filter(Boolean).join(", ");
    let url = "https://www.google.com/maps/search/?api=1&query=" + encodeURIComponent(direccionCompleta);
    window.open(url, "_blank");
  } else {
    return mensajes("error", "Debe llenar la información .");
  }
});
function gestionarCambio(inputElement) {
  if ($("#tipo").val() != "") {
    let clases = $("#identificacion").attr("class").split(" ");
    if (clases.length == 2) {
      if (inputElement.val() == 5) {
        $("#datosRepresentante").hide();
        $("#identificacion").addClass("validacionNumeros");
        return;
      }
      if (inputElement.val() == 4) {
        $("#datosRepresentante").css("display", "block");
        $("#identificacion").addClass("validacionRuc");
        return;
      }
      if (inputElement.val() == 6) {
        $("#datosRepresentante").hide();
        $("#identificacion").addClass("validacionPasaporte");
        return;
      }
      if (inputElement.val() == 8) {
        $("#datosRepresentante").hide();
        $("#identificacion").addClass("validacionCedulaExtranjera");
        return;
      }
    } else {
      $("#identificacion").removeClass(clases[2]);
      gestionarCambio(inputElement);
    }
  }
}
$(document).on("keypress", ".validacionNumeros", function (event) {
  return soloNumeros(event);
});
$(document).on("blur", ".validacionNumeros", function (event) {
  let cantidadCaracteres = $("#identificacion").val();
  if (cantidadCaracteres.length !== 10) {
    return mensajes("error", "La cédula debe tener 10 caracteres");
  }
});
$(document).on("blur", ".validacionRuc", function (event) {
  let cantidadCaracteres = $("#identificacion").val();
  if (cantidadCaracteres.length !== 13) {
    return mensajes("error", "El ruc debe tener 13 caracteres");
  }
});
$(document).on("blur", ".validacionPasaporte", function (event) {
  let regexPasaporte = /^[A-Z]\d{7}$/;

  let pasaporteValue = $(this).val().toUpperCase();

  if (!regexPasaporte.test(pasaporteValue)) {
    return mensajes("error", "Pasaporte con formato invalido");
  }
});
$(document).on("blur", ".validacionCedulaExtranjera", function (event) {
  let cedula = $(this).val();

  if (cedula.length !== 10) {
    return mensajes("error", "Cédula extranjera incorrecta");
  }

  if (cedula[0] < 1 || cedula[0] > 9) {
    return mensajes("error", "Cédula extranjera incorrecta");
  }

  if (cedula[1] < "A" || cedula[1] > "Z") {
    return mensajes("error", "Cédula extranjera incorrecta");
  }

  for (var i = 2; i < cedula.length; i++) {
    if (cedula[i] < 0 || cedula[i] > 9) {
      return mensajes("error", "Cédula extranjera incorrecta");
    }
  }

  let digitoVerificador = 0;
  for (var i = 0; i < cedula.length - 1; i++) {
    digitoVerificador += cedula[i] * (i + 2);
  }
  digitoVerificador = (digitoVerificador % 11) % 10;

  if (cedula[9] !== digitoVerificador) {
    return mensajes("error", "Cédula extranjera incorrecta");
  }

  return true;
});
function verRetencion(id, num) {
  $("#titulo_modal").html('<i class="fas fa-edit"></i> No.: ' + num);
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getRetencionPrint",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "html",
    success: function (html) {
      html = html.split('|');
      $("#contenidoHtml").html(html[0]);
      $('#titulo_modal_retencionesClientes').html('<i class="fas fa-eye" style="color:blue;"></i> Retención No.:' + num);
      $("#myModal").modal("toggle");
    },
    error(e) {
      //window.open("v3/admin", "_self");
      console.log(e);
    },
  });
}
function agregarCruceCuenta() {
  window.open("v3/" + $("#controlador").val() + "/nuevo_cruce_cuentas", "_self");
}
function agregarCruceValores() {
  window.open("v3/" + $("#controlador").val() + "/nuevo_cruce_valores", "_self");
}
function agregarCompIngreso() {
  window.open("v3/" + $("#controlador").val() + "/nuevo_compIngreso", "_self");
}
function agregarCompIngresoVarios() {
  window.open("v3/" + $("#controlador").val() + "/nuevo_compIngresoVarios", "_self");
}
function agregarRetencion() {
  window.open("v3/" + $("#controlador").val() + "/nuevaretencion", "_self");
}

function obtenerFacturaxCliente() {

  let idCliente = $("#clientef").val();
  let data = {
    id: idCliente,
  }
  var json = ajax("v3/" + $("#controlador").val() + "/obtenerFacturaxCliente", data, 'json');

  if (json.responseJSON != '') {
    json = json.responseJSON;
    json.forEach(option => {
      let optionElement = document.createElement("option");
      optionElement.value = option.id;
      optionElement.text = option.numero + ' | ' + option.nombre + ' | ' + option.concepto;
      document.getElementById("facturas").appendChild(optionElement);
    });
  } else {
    document.getElementById("facturas").innerHTML = "";
    let optionElement = document.createElement("option");
    optionElement.value = "";
    optionElement.text = "--Seleccione--";
    document.getElementById("facturas").appendChild(optionElement);
    obtenerFactura();
  }
}


function obtenerFactura() {
  var d = $('#facturas').val().split('|');
  var data = {
    id: d[0],
  }
  var json = ajax("v3/" + $("#controlador").val() + "/obtenerFactura", data, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    let seriec = json.sucursal == undefined ? '' : json.sucursal + '-' + json.punto_emision;
    console.log(seriec);
    $('#id_factura').val(json.id !== undefined ? json.id : '');
    $('#fecha').val(json.fecha !== undefined ? json.fecha : '');
    $('#numero').val(json.numero !== undefined ? json.numero : '');
    $('#autorizacion').val(json.nro_autorizacion !== undefined ? json.nro_autorizacion : '');
    $('#tipo_comprobante').val(json.tipo_comprobante !== undefined ? json.tipo_comprobante : '');
    $('#tipo').val(json.tipo !== undefined ? json.tipo : '');
    $('#id_cliente_').val(json.id_cliente !== undefined ? json.id_cliente : "");
    $('#valor_contable').val((parseFloat(json.valor_contable !== undefined ? json.valor_contable : 0)).toFixed(2));
    $('#cliente').val(json.nombre_cliente !== undefined ? json.nombre_cliente : "");
    $('#concepto').val(json.sucursal !== undefined ? "FACT#" + json.sucursal + '-' + json.punto_emision + '-' + json.numero : "");
    $('#serie').val(seriec);
    $("#subtotal").val(parseFloat(parseFloat(json.subtotal_0).toFixed(2) - parseFloat(json.subtotal_12).toFixed(2)).toFixed(2));
    $('#baseRenta').val(parseFloat(json.subtotal_0 !== undefined ? parseFloat(json.subtotal_0) + parseFloat(json.subtotal_12) : 0).toFixed(2));
    $('#baseIva').val(parseFloat(json.impuesto !== undefined ? json.impuesto : 0).toFixed(2));
    $('#iva').val(parseFloat(json.impuesto).toFixed(2));
  }
}
function agregarImpuesto() {
  if ($('#facturas').val() != '') {
    filaImpuesto++;
    var html = '';
    html += '<tr id="tr_' + filaImpuesto + '">';
    html += '<td><select class="form-control select2" style="width: 100%;" id="tipo_retencion' + filaImpuesto + '" name="tipo_retencion[]" onchange="traerPorcentaje(\'' + filaImpuesto + '\')"><option value="">--Seleccione--</option><option value="RENTA">Renta</option><option value="IVA">Iva</option></select></td>';
    html += '<td><select class="form-control select2_porcentaje" style="width: 100%;" id="porcentaje_retencion' + filaImpuesto + '" name="porcentaje_retencion[]" onchange="asignarCodigo(\'' + filaImpuesto + '\')"><option value="">--Seleccione--</option></select></td>';
    html += '<td><input type="text" class="form-control text-right" id="codigo_retencion' + filaImpuesto + '" name="codigo_retencion[]" placeholder="-" readonly /></td>';
    html += '<td><input type="text" class="form-control text-right" id="base_retencion' + filaImpuesto + '" name="base_retencion[]" placeholder="0.00" readonly /></td>';
    html += '<td><input type="text" class="form-control text-right" id="valor_retencion' + filaImpuesto + '" name="valor_retencion[]" placeholder="0.00" /></td>';
    html += '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarImpuesto(\'' + filaImpuesto + '\');"><i class="fa fa-trash-alt"></i></a></td>';
    html += '</tr>';
    $('#impuestos').append(html);
  } else {
    mensajes('info', 'Debe seleccionar una factura');
  }
}
function eliminarImpuesto(id) {
  $("#tr_" + id).remove();
  filaImpuesto--;
  calcularRetencion();
}
function traerPorcentaje(fil) {
  var data = {
    tipo: $('#tipo_retencion' + fil).val(),
  };
  var json = ajax("v3/" + $("#controlador").val() + "/obtenerPorcentajeRetenciones", data, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    if ($('#tipo_retencion' + fil).val() != "") {
      $('#porcentaje_retencion' + fil).empty();
      $("#porcentaje_retencion" + fil).append('<option value="">--Seleccione--</option>');
      $.each(json, function (key, registro) {
        $("#porcentaje_retencion" + fil).append('<option value="' + registro.id + '|' + registro.codigo_nombre + '|' + registro.valor + '">' + registro.nombre + ' | ' + registro.valor + '</option>');
      });
      if ($('#tipo_retencion' + fil).val() == 'RENTA') {
        $('#base_retencion' + fil).val($('#baseRenta').val());
      }
      else {
        $('#base_retencion' + fil).val($('#baseIva').val());
      }
      $("#porcentaje_retencion" + fil).select2();
    } else {
      $("#porcentaje_retencion" + fil).empty();
      $("#porcentaje_retencion" + fil).append('<option value="">--Seleccione--</option>');
    }
  }
}
function asignarCodigo(fil) {
  var d = $("#porcentaje_retencion" + fil).val().split('|');
  var valorRetencion = (parseFloat(d[2]) * parseFloat($('#base_retencion' + fil).val())) / 100;
  $('#codigo_retencion' + fil).val(d[2]);
  $('#valor_retencion' + fil).val(valorRetencion);
  calcularRetencion();
}
function calcularRetencion() {
  var renta = 0;
  var iva = 0;
  $("#frm_").find(':input').each(function () {
    var elemento = this;
    if (/tipo_retencion/.test(elemento.id)) {
      var to = elemento.id.replace('tipo_retencion', '');
      var d = $("#" + elemento.id).val();
      if (d == 'RENTA') {
        renta += parseFloat($('#valor_retencion' + to).val());
      } else if (d == 'IVA') {
        iva += parseFloat($('#valor_retencion' + to).val());
      }
    }
  });
  $('#renta').val(renta);
  $('#iva').val(iva);
  $('#total_r').val(parseFloat(parseFloat(renta) + parseFloat(iva)).toFixed(2));
}
$("#agregarLineaCruce").on("click", function () {
  var html = '<tr id="tr_' + filaCruce + '">';
  html += '<td style="max-width: 100%;"><select class="form-control select2_cuenta" id="id_cuenta_' + filaCruce + '" name="cuenta[]"><option value="">--Seleccione--</option></select></td>';
  html += '<td><input type="text" class="form-control " id="detalle_' + filaCruce + '" name="detalle[]" value="" " /></td>';
  html += '<td><input type="text" class="form-control " id="divisa_' + filaCruce + '" name="divisa[]" value="USD" " readonly/></td>';
  html += '<td><input type="text" class="form-control text-right" id="valor_' + filaCruce + '" name="valor[]" value="0.00" onchange="valor_rubroCruce(' + filaCruce + ')" "/></td>';
  html += '<td><input type="text" class="form-control text-right" id="total_base_' + filaCruce + '" name="total_base[]" value="0.00" " readonly/></td>';
  html += '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarFila(\'' + filaCruce + '\');"><i class="fa fa-trash-alt"></i></a></td>';
  html += "</tr>";
  $("#detalleCruceCuentas").before(html);
  $(".select2_cuenta").select2({
    width: "element",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarCuenta",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        return {
          results: $.map(response, function (categoria) {
            return { id: categoria.plan, text: categoria.text };
          }),
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  filaCruce++;
});

$("#agregarLineaciv").on("click", function () {
  var uniqueId2 = filaCruceCiv;
  filaCruceCivIds.push(uniqueId2);

  var html = '<tr id="tr_' + uniqueId2 + '">';
  html += '<td style="max-width: 100%;"><select class="form-control select2_cuenta_CIV" id="id_cuenta_contable_' + uniqueId2 + '" name="cuenta_contable[]"><option value="">--Seleccione--</option></select></td>';
  html += '<td><input type="text" class="form-control " id="divisa_' + uniqueId2 + '" name="divisa[]" value="USD" " readonly/></td>';
  html += '<td><input type="text" class="form-control text-right" id="debe_' + uniqueId2 + '" name="debe[]" value="0.00" " readonly/></td>';
  html += '<td><input type="text" class="form-control text-right" id="haber_' + uniqueId2 + '" name="haber[]" value="0.00" onchange="envalor_base(' + uniqueId2 + ')"/></td>';
  html += '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarFilaciv(\'' + uniqueId2 + '\');"><i class="fa fa-trash-alt"></i></a></td>';
  html += "</tr>";
  $("#detalleComplementociv").before(html);
  $(".select2_cuenta_CIV").select2({
    width: "100%",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarCuenta",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        return {
          results: $.map(response, function (categoria) {
            return { id: categoria.plan, text: categoria.text };
          }),
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  filaCruceCiv++;
  aux_filaCruceCiv++;
});

function eliminarFila(idCruce) {
  $("#tr_" + idCruce).remove();
  filaCruce--;
  sumarCruce();
}
function eliminarFilaciv(idCruce) {
  $("#tr_" + idCruce).remove();
  aux_filaCruceCiv--;
  filaCruceCivIds = filaCruceCivIds.filter(function (item) {
    return item != id;
  });
  suma_totales_civ();
}
function validarCodigo() {
  if ($("#codigo").val() != '') {
    return;
  }
  var data = {
    codigo: $("#codigo").val(),
  };
  var json = ajax("v3/" + $("#controlador").val() + "/verificarRubro", data, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    if (json.status == "error") {
      return mensajes("warning", json.message);
    }
  }
}
function valor_rubroCruce(id) {
  var e = parseFloat($("#valor_" + id).val());
  if (e == "" || isNaN(e) || e <= 0) {
    e = 0;
  }
  $("#valor_" + id).val(parseFloat(e).toFixed(2, 2));
  $("#total_base_" + id).val(parseFloat(e).toFixed(2, 2));
  sumarCruce(id);
}
function valor_vr(id) {
  var abono = parseFloat($("#valor" + id).val());
  if (isNaN(abono) || abono <= 0) {
    $("#valor" + id).val(parseFloat(0).toFixed(2));
  }
  suma_totales();
  var valor = parseFloat($("#valor_total").val());
  var cantidad = parseFloat($("#total_suma").val());
  if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
    if (cantidad <= valor) {
      $("#valor" + id).val(abono.toFixed(2));
    } else {
      valor = 0;
      $("#valor" + id).val(valor.toFixed(2));
      mensajes("warning", "El valor no puede superar al total de ingresos");
    }
  } else {
    valor = 0;
    $("#valor" + id).val(valor.toFixed(2));
  }
  suma_totales();
}

function suma_totales() {
  var total = 0;
  $("input[name='valor[]']").each(function () {
    var cantidad = parseFloat($(this).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#total").val(total.toFixed(2));
  $("#total_suma").val(total.toFixed(2));
}

function valor_civ(id) {
  var abono = parseFloat($("#valor" + id).val());
  if (abono == "" || isNaN(abono) || abono <= 0) {
    abono = 0;
  }
  $("#valor" + id).val(parseFloat(abono).toFixed(2));
  suma_totales_civ();
}

function suma_totales_civ() {
  var total = 0;
  $("input[name='valor[]']").each(function () {
    var cantidad = parseFloat($(this).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#total_ingresos").val(total.toFixed(2, 2));
}

function suma_totales_cv() {
  total = 0;
  cantidad = 0;
  $("input[name='saldo_cv[]']").each((i, v) => {
    cantidad = parseFloat($("#abono_a" + i).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#total_suma").val(total.toFixed(2, 2));
}
function suma_totales2_cv() {
  total = 0;
  cantidad = 0;
  $("input[name='saldo_cv[]']").each((i, v) => {
    cantidad = parseFloat($("#abono_cv" + i).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#total_suma_a").val(total.toFixed(2, 2));
}
function suma_totales3_cv() {
  total = 0;
  cantidad = 0;
  $("input[name='saldo_cv[]']").each((i, v) => {
    cantidad = parseFloat($("#saldo_cv" + i).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#saldoax").val(total.toFixed(2, 2));
}
function suma_totales_deuda_cv() {
  total = 0;
  cantidad = 0;
  $("input[name='saldo_deuda_cv[]']").each((i, v) => {
    cantidad = parseFloat($("#abono_deuda_cv" + i).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#total_suma_cv").val(total.toFixed(2, 2));
}
function validar_abono(id) {
  var valor = parseFloat($("#valor_total").val());
  var abono = parseFloat($("#abono_a" + id).val());
  var saldo = parseFloat($("#saldo_a" + id).val());
  if (abono == "" || isNaN(abono) || abono <= 0) {
    $("#abono_a" + id).val(parseFloat(0).toFixed(2))
  }
  suma_totales2();
  var cantidad = parseFloat($("#total_suma_a").val());
  if (abono > 0 && !isNaN(valor) && !isNaN(cantidad)) {
    if (cantidad <= valor) {
      $("#verificar_superavit").val(1);
      if (abono > saldo) {
        abono = saldo;
      }
      var tot = saldo - abono;
      $("#abono_a" + id).val(abono.toFixed(2, 2));
      $("#nuevo_saldo" + id).val(tot.toFixed(2, 2));
    } else {
      valor = 0;
      $("#abono_a" + id).val(valor.toFixed(2, 2));
      $("#nuevo_saldo" + id).val(saldo.toFixed(2, 2));
      return mensajes("warning", "El abono no puede superar al valor");
    }
  } else {
    abono = 0;
    valor = 0;
    $("#abono_a" + id).val(valor.toFixed(2, 2));
    $("#nuevo_saldo" + id).val(saldo.toFixed(2, 2));
  }
}
function validar_abonocv(id) {
  var valor = parseFloat($("#saldo_cv" + id).val());
  var abono = parseFloat($("#abono_cv" + id).val());
  if (abono == "" || isNaN(abono) || abono < 0) {
    $("#abono_cv" + id).val(parseFloat(0).toFixed(2))
  }
  suma_totales2_cv();
  suma_totales3_cv();
  var sumax = parseFloat($("#saldoax").val());
  var total = parseFloat($("#total_anticipos").val());
  if (isNaN(total)) {
    total = 0;
  }
  var cantidad = parseFloat($("#total_suma_a").val());
  if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
    if (abono > 0) {
      if (abono <= valor && cantidad <= sumax) {
        $("#total_anticipos").val(cantidad.toFixed(2, 2));
        suma_totales2_cv();
        $("#abono_cv" + id).val(abono.toFixed(2, 2));
        var totalx = valor - abono;
        $("#nuevo_saldo_cv" + id).val(totalx.toFixed(2, 2));
      } else {
        valor = 0;
        $("#abono_cv" + id).val(valor.toFixed(2, 2));
        $('#nuevo_saldo_cv' + id).val(valor.toFixed(2, 2));
        suma_totales2_cv();
        suma_totales_cv();
        var cantidad = parseFloat($("#total_suma_a").val());
        $("#total_anticipos").val(cantidad.toFixed(2, 2));
        return mensajes("warning", "El abono no puede superar al saldo");
      }
    } else {
      suma_totales_cv();
      $("#nuevo_saldo_cv" + id).val(abono.toFixed(2, 2));
      $("#abono_cv" + id).val(abono.toFixed(2, 2));
      $("#total_anticipos").val(cantidad.toFixed(2, 2));
    }
  } else {
    valor = 0;
    $("#nuevo_saldo_cv" + id).val(valor.toFixed(2, 2));
    $("abono_cv" + id).val(valor.toFixed(2, 2));
  }
}
function validar_td_cv(id) {
  if ((id) != null) {
    var valor = parseFloat($("#total_anticipos").val());
    var abono = parseFloat($("#abono_deuda_cv" + id).val());
    var saldo = parseFloat($("#saldo_deuda_cv" + id).val());
    if (abono == "" || isNaN(abono) || abono < 0) {
      $("#abono_deuda_cv" + id).val(parseFloat(0).toFixed(2))
    }
    suma_totales_deuda_cv();
    var cantidad = parseFloat($("#total_suma_cv").val());
    if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
      if (cantidad <= valor) {
        if (abono <= saldo) {
          var tot = saldo - abono;
          $("#abono_deuda_cv" + id).val(abono.toFixed(2, 2));
          $("#nuevo_saldo_deuda_cv" + id).val(tot.toFixed(2, 2));
        } else {
          var tot = saldo - abono;
          $("#abono_deuda_cv" + id).val(saldo.toFixed(2, 2));
          $("#nuevo_saldo_deuda_cv" + id).val(tot.toFixed(2, 2));
        }
      } else {
        valor = 0;
        $("#abono_deuda_cv" + id).val(valor.toFixed(2, 2));
        $("#nuevo_saldo_deuda_cv" + id).val(valor.toFixed(2, 2));
        suma_totales_deuda_cv();
        return mensajes("warning", "El abono no puede superar al saldo");
      }
    } else {
      valor = 0;
      $("abono_deuda_cv" + id).val(valor.toFixed(2, 2));
    }
  } else {
    return mensajes("error", "Error");
  }
}
function suma_totales2() {
  var total = 0;
  $('.abonos').each(function (i, obj) {
    total += parseFloat($(this).val());
  });
  $("#total_suma_a").val(total.toFixed(2));
  var cheque = parseFloat($("#valor_total").val());
  var restante = cheque - total;
  if (restante == "" || isNaN(restante) || (restante < 0)) {
    restante = 0;
  }
  $("#restante").val(restante.toFixed(2));
}
function sumarCruce(id) {
  var valor = 0;
  var total = 0;
  var sub = 0;
  $("input[name='valor[]']").each((i, v) => {
    valor += Number(v.value);
  });
  sub = sub + valor;
  total = total + valor;
  total_final = parseFloat(total.toFixed(2, 2));
  if ($("#info").val() >= total_final) {
    if (!isNaN(sub)) {
      $('#subtotal').val(sub.toFixed(2));
    }
    if (!isNaN(total)) {
      $('#total').val(total.toFixed(2));
    }
  } else {
    $("#valor_" + id).val(parseFloat(0).toFixed(2, 2));
    $("#total_base_" + id).val(parseFloat(0).toFixed(2, 2));
    sumarCruce(id);
    return mensajes("warning", "El total no dede ser mayor al valor de la factura");
  }
}
function PagoEliminar(id) {
  $("#trPago_" + id).remove();
  filaCompIngreso--;
}

function PagoEliminarciv(id) {
  $("#trPago_" + id).remove();
  aux_filaCompIngresoVarios--;
  filaCompIngresoIds = filaCompIngresoIds.filter(function (item) {
    return item != id;
  });
  suma_totales_civ();
}

function asignarPago(id) {
  $("#id_bancos_" + id).val(null).trigger("change");
  $("#id_tarjetas_" + id).val(null).trigger("change");
  $("#numero_" + id).val("");
  $("#cuenta_" + id).val("");
  //$("#giradoA_" + id).val("");
  const valor = $("#id_pago_" + id).val();
  const isValor1 = valor == 1;
  const isValor2 = valor == 2;
  const isValor3 = valor == 3;
  const isValor4 = valor == 4;
  const isValor5 = valor == 5;
  const isValor6 = valor == 6;
  const isValor7 = valor == 7;
  const isValor8 = valor == 8;
  const isValor9 = valor == 9;
  const isValor10 = valor == 10;
  if (isValor1 || isValor8 || isValor9 || isValor10 || isValor7 || isValor2 || isValor3 || isValor5) {
    //$("#id_tarjetas_" + id).closest('td').hide();
    $("#id_tarjetas_" + id).closest('td').css('visibility', 'hidden');
  } else {
    //$("#id_tarjetas_" + id).closest('td').show();
    $("#id_tarjetas_" + id).closest('td').css('visibility', 'visible');
  }
  if (isValor1 || isValor8 || isValor9 || isValor10 || isValor7) {
    $("#id_bancos_" + id).closest('td').css('visibility', 'hidden');
  } else {
    $("#id_bancos_" + id).closest('td').css('visibility', 'visible');
  }
  $("#numero_" + id).toggle(!(isValor1 || isValor8 || isValor9 || isValor10 || isValor7 || isValor4 || isValor6));
  $("#cuenta_" + id).toggle(!(isValor1 || isValor8 || isValor9 || isValor10 || isValor7 || isValor3 || isValor5 || isValor6 || isValor7 || isValor4));
  //$("#giradoA_" + id).toggle(!(isValor1 || isValor8 || isValor9 || isValor10 || isValor7 || isValor3 || isValor4 || isValor6 || isValor7 || isValor5));
  // Obtener campos habilitados
  const camposHabilitados = [
    "#id_tarjetas_" + id,
    "#numero_" + id,
    "#id_bancos_" + id,
    "#cuenta_" + id,
    "#giradoA_" + id
  ];
}
function buscar_detdeuda() {
  e = 0;
  $("#valor_total").val(parseFloat(e).toFixed(2))
  $("#total").val(parseFloat(e).toFixed(2))
  $("#restante").val(parseFloat(e).toFixed(2))
  $("#detalleDeudaCliente").empty();
  var proveedor = $("#id_cliente").val();
  var data = {
    'cliente': proveedor,
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getDetalleDeudaCliente", data, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    var tablaHtml = '';
    for (var i = 0; i < json.length; i++) {
      tablaHtml += '<tr style="background-color: white"> <td><input class="form-control" style="text-align: center;" type="text" name="id_fac[]" id="id_fac' + i + '" readonly value="' + json[i].id + '"></td>';
      tablaHtml += '</td> <td align="center"><input class="form-control text-center" type="text" name="emision[]" id="emision' + i + '" value="' + json[i].fecha + '" readonly></td>';
      tablaHtml += '</td> <td align="center"><input class="form-control text-center" type="text" name="vencimiento[]" id="vencimiento' + i + '" value="' + json[i].fecha_vencimiento + '" readonly></td>';
      tablaHtml += '<td> <input class="form-control text-center" type="text" name="tipo_a[]" id="tipo_a' + i + '" value="' + json[i].tipo + '" readonly> </td>';
      tablaHtml += '<input class="form-control" type="hidden" name="id_actualiza[]" value="' + json[i].id + '" readonly>';
      tablaHtml += '<td> <input class="form-control text-center" type="text" name="numero_fact[]" id="numero_fact' + i + '" value="' + json[i].nro_comprobante + '" readonly> </td>';
      tablaHtml += '<td> <input class="form-control" type="text" name="observacion[]" id="observacion' + i + '" title="Fact: ' + json[i].nro_comprobante + ' Ref: ' + json[i].concepto + '" value="Fact:' + json[i].nro_comprobante + ' Ref: ' + json[i].concepto + '" readonly> </td>';
      //tablaHtml += '<td> <input class="form-control" type="text" value="$" readonly> </td>';
      tablaHtml += '<td> <input class="form-control" type="text" style="text-align: right;" name="saldo_a[]" value="' + json[i].valor_contable + '" id="saldo_a' + i + '" readonly> </td>';
      tablaHtml += '<td> <input class="form-control abonos" type="text" style="text-align: right;" name="abono_a[]" id="abono_a' + i + '" onchange="validar_abono(' + i + ')" value="0.00"> </td>';
      tablaHtml += '<td> <input  class="form-control" type="text" style="text-align: right;" name="nuevo_saldo[]" value="' + json[i].valor_contable + '" id="nuevo_saldo' + i + '" readonly></td>';
      tablaHtml += '</tr>';
    }
    $("#detalleDeudaCliente").html(tablaHtml);
  }
}
function buscar_anticipos() {
  $("#detalleValoresFavor").empty();
  var proveedor = $("#id_cliente").val();
  var data = { 'cliente': proveedor };
  var json = ajax("v3/" + $("#controlador").val() + "/getObtenerAnticipos", data, 'json');
  if (json.responseJSON != null) {
    var detalleJson = json.responseJSON;
    if (detalleJson != null && detalleJson.length > 0) {
      tablaHtml = '';
      for (var i = 0; i < detalleJson.length; i++) {
        tablaHtml += '<tr style="background-color: white"> <input class="form-control" style="text-align: center;" type="hidden" name="idac[]" id="idac' + i + '" readonly value="' + detalleJson[i].id + '">';
        tablaHtml += '<td><input class="form-control" type="text" name="emision[]" id="emision' + i + '" value="' + detalleJson[i].fecha + '" readonly></td>';
        tablaHtml += '<td> <input class="form-control" type="text" name="tipo[]" id="tipo' + i + '" value="CLI-IN" readonly> </td>';
        tablaHtml += '<td> <input class="form-control" type="text" name="numero_a[]" id="numero_a' + i + '" value="' + detalleJson[i].secuencia + '" readonly> </td>';
        tablaHtml += '<td> <input class="form-control" type="text" name="concepto_a[]" id="concepto_a' + i + '" title="Fact: ' + detalleJson[i].observaciones + '" value="Fact: ' + detalleJson[i].observaciones + '" readonly> </td>';
        tablaHtml += '<td> <input class="form-control" type="text" value="$" readonly> </td>';
        tablaHtml += '<td> <input class="form-control" type="text" style="text-align: right;" name="saldo_cv[]" value="' + detalleJson[i].deficit_ingreso + '" id="saldo_cv' + i + '" readonly> </td>';
        tablaHtml += '<td> <input class="form-control " type="text" style="text-align: right;" name="abono_cv[]" id="abono_cv' + i + '" onchange="validar_abonocv(' + i + ')" value="0.00"> </td>';
        tablaHtml += '<td> <input  class="form-control" type="text" style="text-align: right;" name="nuevo_saldo_cv[]" value="0.00" id="nuevo_saldo_cv' + i + '" readonly></td>';
        tablaHtml += '</tr>';
      }
      tablaHtml += '</tbody></table>';
      $("#detalleValoresFavor").html(tablaHtml);
    }
  }
  var json1 = ajax("v3/" + $("#controlador").val() + "/getDetalleDeudaCliente", data, 'json');
  if (json1.responseJSON != null) {
    var detalleJson1 = json1.responseJSON;
    if (detalleJson1 != null && detalleJson1.length > 0) {
      var tablaHtml1 = '';
      for (var i = 0; i < detalleJson1.length; i++) {
        tablaHtml1 += '<tr style="background-color: white"> <input class="form-control" style="text-align: center;" type="text" name="id_fac[]" id="id_fac' + i + '" readonly value="' + detalleJson1[i].id + '">';
        tablaHtml1 += '</td> <td><input class="form-control" type="text" name="emision[]" id="emision' + i + '" value="' + detalleJson1[i].fecha + '" readonly></td>';
        tablaHtml1 += '<td> <input class="form-control" type="text" name="tipo_a[]" id="tipo_a' + i + '" value="' + detalleJson1[i].tipo + '" readonly> </td>';
        tablaHtml1 += '<input class="form-control" type="hidden" name="id_actualiza[]" value="' + detalleJson1[i].id + '" readonly>';
        tablaHtml1 += '<td> <input class="form-control" type="text" name="numero_fact[]" id="numero_fact' + i + '" value="' + detalleJson1[i].nro_comprobante + '" readonly> </td>';
        tablaHtml1 += '<td> <input class="form-control" type="text" name="observacion[]" id="observacion' + i + '" title="Fact:' + detalleJson1[i].nro_comprobante + ' Ref: ' + detalleJson1[i].concepto + '" value="Fact:' + detalleJson1[i].nro_comprobante + ' Ref: ' + detalleJson1[i].concepto + '" readonly> </td> ';
        tablaHtml1 += '<td> <input class="form-control" type="text" value="$" readonly> </td>';
        tablaHtml1 += '<td> <input class="form-control" type="text" style="text-align: right;" name="saldo_deuda_cv[]" value="' + detalleJson1[i].valor_contable + '" id="saldo_deuda_cv' + i + '" readonly> </td>';
        tablaHtml1 += '<td> <input class="form-control abonos" type="text" style="text-align: right;" name="abono_deuda_cv[]" id="abono_deuda_cv' + i + '" onchange="validar_td_cv(' + i + ')" value="0.00"> </td>';
        tablaHtml1 += '<td> <input  class="form-control" type="text" style="text-align: right;" name="nuevo_saldo_deuda_cv[]" value="' + detalleJson1[i].valor_contable + '" id="nuevo_saldo_deuda_cv' + i + '" readonly></td>';
        tablaHtml1 += '</tr>';
      }
      $("#detalleDeudaCliente").html(tablaHtml1);
    }
  }
}
function verCompIngreso(id_compIngreso) {
  limpiarModal();
  $("#titulo_modal").html('<i class="fas fa-eye"></i> Visualizador comprobante de ingreso');
  var data = {
    id: id_compIngreso,
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getCompIngreso", data, 'json');
  if (json.responseJSON != null) {
    json = json.responseJSON;
    $("#id").val(json.id);
    $("#numero").val(json.secuencia);
    $("#asiento").val(json.id_asiento_cabecera);
    $("#fecha").val(json.fecha);
    $("#valor_total").val(json.total_ingreso);
    $("#secuencia").val(json.sc);
    $("#os").val(json.os);
    $("#id_vendedor1").val(
      (json.id_vendedor !== null ? json.id_vendedor + ' | ' : '') +
      (json.v_ap1 !== null ? json.v_ap1 + ' ' : '') +
      (json.v_ap2 !== null ? json.v_ap2 + ' ' : '') +
      (json.v_nom1 !== null ? json.v_nom1 + ' ' : '') +
      (json.v_nom2 !== null ? json.v_nom2 : '')
    );
    $("#id_cliente1").val(json.id_cliente + ' | ' + json.nombre_cliente);
    $("#total").val(json.total_ingreso);
    $("#autorellenar").val(json.observaciones);
    $("#detalleValoresRecibidos").empty();
    var detalle = {
      id: id_compIngreso,
    };
    var detalleJson = ajax("v3/" + $("#controlador").val() + "/getDetalleValorRecibido", detalle, 'json');
    if (detalleJson.responseJSON != null) {
      detalleJson = detalleJson.responseJSON;
      var tablaHtml = '';
      for (var i = 0; i < detalleJson.length; i++) {
        tablaHtml += '<tr><td style="width: 11%; text-align: center;">' + detalleJson[i].tipo_pago + '</td>';
        tablaHtml += '<td style="width: 8%; text-align: center;">' + detalleJson[i].fecha + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: center;">' + (detalleJson[i].numero !== null ? detalleJson[i].numero : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 15%; text-align: center;">' + (detalleJson[i].banco !== null ? detalleJson[i].banco : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 14%; text-align: center;">' + (detalleJson[i].tarjeta !== null ? detalleJson[i].tarjeta : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: center;">' + (detalleJson[i].cuenta !== null ? detalleJson[i].cuenta : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 13%;">' + (detalleJson[i].girador !== null ? detalleJson[i].girador : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: right;">' + detalleJson[i].total + '</td>';
        tablaHtml += '</tr>';
      }

      $("#detalleValoresRecibidos").html(tablaHtml);
    }
    var detCliente = {
      id: id_compIngreso,
    };
    var detalleJson1 = ajax("v3/" + $("#controlador").val() + "/getDetalleDCliente", detCliente, 'json');
    if (detalleJson1.responseJSON != null) {
      detalleJson1 = detalleJson1.responseJSON;
      var tablaHtml = '';
      for (var i = 0; i < detalleJson1.length; i++) {
        tablaHtml += '<tr><td style="width: 8%; text-align: center;">' + detalleJson1[i].factura + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: center;">' + detalleJson1[i].fecha + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: center;">' + detalleJson1[i].fecha_vencimiento + '</td>';
        tablaHtml += '<td style="width: 8%; text-align: center;">' + (detalleJson1[i].tipo !== null ? detalleJson1[i].tipo : 'VEN-FA') + '</td>';
        tablaHtml += '<td style="width: 10%;">' + detalleJson1[i].secuencia_factura + '</td>';
        tablaHtml += '<td style="width: 30%;">' + detalleJson1[i].observaciones + '</td>';
        //tablaHtml += '<td style="width: 5%;"> $ </td>';
        tablaHtml += '<td style="width: 10%; text-align: right;">' + detalleJson1[i].total_factura + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: right;">' + detalleJson1[i].total + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: right;">' + (detalleJson1[i].total_factura - detalleJson1[i].total) + '</td>';
        tablaHtml += '</tr>';
      }
      $("#detalleDeudaCliente").html(tablaHtml);
    }
    $("#myModal").modal("toggle");
  }
}
function verCruceValores(id_cruce) {
  limpiarModal();
  $("#titulo_modal").html('<i class="fas fa-eye"></i> Visualizador cruce de valores a favor');
  var data = {
    id: id_cruce,
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getCruceValores", data, 'json');

  if (json.responseJSON != null) {
    json = json.responseJSON;
    $("#id").val(json.id);
    $("#numero").val(json.secuencia);
    $("#asiento").val(json.id_asiento_cabecera);
    $("#fecha").val(json.fecha);
    $("#id_cliente1").val(json.id_cliente + ' | ' + json.nombre);
    $("#total").val(json.total_disponible);
    $("#concepto").val(json.detalle);
    $("#detalleValoresaFavor").empty();
    var detalle = {
      id: id_cruce,
    };
    var detalleJson = ajax("v3/" + $("#controlador").val() + "/getDetalleValoraFavor", detalle, 'json');
    if (detalleJson.responseJSON != null) {
      detalleJson = detalleJson.responseJSON;
      var tablaHtml = '';
      for (var i = 0; i < detalleJson.length; i++) {
        tablaHtml += '<tr><td style="text-align: center;">' + detalleJson[i].fecha + '</td>';
        tablaHtml += '<td style="text-align: center;"> CLI-IN </td>';
        tablaHtml += '<td style="text-align: left;">' + (detalleJson[i].numero !== null ? detalleJson[i].numero : '&nbsp;') + '</td>';
        tablaHtml += '<td style="text-align: left;">' + (detalleJson[i].concepto !== null ? detalleJson[i].concepto : '&nbsp;') + '</td>';
        tablaHtml += '<td style="text-align: left;"> $ </td>';
        tablaHtml += '<td style="text-align: right;">' + (detalleJson[i].valor_ant !== null ? detalleJson[i].valor_ant : '&nbsp;') + '</td>';
        tablaHtml += '<td style="text-align: right;">' + (detalleJson[i].valor !== null ? detalleJson[i].valor : '&nbsp;') + '</td>';
        tablaHtml += '<td style="text-align: right;">' + (detalleJson[i].valor_ant !== null ? detalleJson[i].valor_ant - detalleJson[i].valor : '&nbsp;') + '</td>';
        tablaHtml += '</tr>';
      }

      $("#detalleValoresaFavor").html(tablaHtml);
    }
    var detCliente = {
      id: id_cruce,
    };
    var detalleJson1 = ajax("v3/" + $("#controlador").val() + "/getDetalleClienteValores", detCliente, 'json');
    if (detalleJson1.responseJSON != null) {
      detalleJson1 = detalleJson1.responseJSON;
      var tablaHtml = '';
      for (var i = 0; i < detalleJson1.length; i++) {
        tablaHtml += '<tr><td text-align: center;">' + detalleJson1[i].fecha + '</td>';
        tablaHtml += '<td text-align: center;"> VEN-FA </td>';
        tablaHtml += '<td style="text-align: left;">' + detalleJson1[i].secuencia_factura + '</td>';
        tablaHtml += '<td style="text-align: left;">' + detalleJson1[i].observaciones + '</td>';
        tablaHtml += '<td style="text-align: left;"> $ </td>';
        tablaHtml += '<td style="text-align: right;">' + detalleJson1[i].total_factura + '</td>';
        tablaHtml += '<td style="text-align: right;">' + detalleJson1[i].total + '</td>';
        tablaHtml += '</tr>';
      }
      $("#detalleDeudaClientecvalores").html(tablaHtml);
    }
    $("#myModal").modal("toggle");
  }
}
function anularCruce(id) {
  Swal.fire({
    title: '¿Desea Anular este comprobante?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      test(id);
    }
  });
}
async function test(id) {
  try {
    const {
      value: text
    } = await Swal.fire({
      input: 'textarea',
      inputPlaceholder: 'Ingrese motivo de anulación...',
      inputAttributes: {
        'aria-label': 'Ingrese motivo de anulación'
      },
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Anular'
    })

    if (text) {
      $.ajax({
        url: "v3/" + $('#controlador').val() + "/anularCruce",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          text: text
        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, '', '', '', 'tabla');
        },
        error: function (json) {
          console.log(json);
        },
      });
    }
  } catch (err) {
    console.log(err);
  }
}
function boton_deuda() {
  if ($("#id_cliente").val() == "") {
    return mensajes("warning", "Seleccione un cliente para continuar");
  } else if ($("#valor_total").val() <= 0) {
    return mensajes("warning", "Ingrese el valor");
  }
  var valor = parseFloat($("#valor_total").val());
  var valor2 = parseFloat($("#valor_total").val());
  var valor_saldo = parseFloat($("#saldo0").val());
  var saldo = 0
  var total = 0;
  var cero = 0;
  $('.abonos').each(function (i, obj) {
    saldo += parseFloat($("#saldo_a" + i).val());
    valor_saldo = parseFloat($("#saldo_a" + i).val());
    valor -= valor_saldo;
    var cont = parseFloat($("#abono_a" + i).val());
    if (isNaN(cont)) {
      cont = 0;
    }
    if (valor > valor_saldo) {
      $("#abono_a" + i).val(valor_saldo.toFixed(2, 2));
      $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
    } else {
      total = valor + valor_saldo;
      if (total <= valor2) {
        if (total > 0) {
          if (total > valor_saldo) {
            total = valor_saldo;
            $("#abono_a" + i).val(total.toFixed(2, 2));
            $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
          } else {
            $("#abono_a" + i).val(total.toFixed(2, 2));
            var saldo = parseFloat($('#saldo_a' + i).val()) - total;
            $("#nuevo_saldo" + i).val(saldo.toFixed(2, 2));
          }
        }
      }
    }
  });
  var total = 0;
  if (!isNaN(valor) && !isNaN(valor_saldo)) {
    $("#verificar_superavit").val(1);
  }
  // else {
  //   return mensajes("warning", "Ingrese el valor");
  // }
  suma_totales2();
}
function setNumber() {
  cero = 0;
  var e = parseFloat($("#valor_total").val());
  if (e == "" || isNaN(e) || e <= 0) {
    e = 0;
  }
  $('.abonos').each(function (i, obj) {
    $(this).val(cero.toFixed(2, 2))
    var saldo = parseFloat($('#saldo_a' + i).val());
    $("#nuevo_saldo" + i).val(saldo.toFixed(2, 2));
  });
  $("#total_suma_a").val(cero.toFixed(2, 2));

  $("#valor_total").val(parseFloat(e).toFixed(2))
  //$("#total").val(parseFloat(e).toFixed(2))
  $("#restante").val(parseFloat(e).toFixed(2))

}
function autollenarf() {
  var acumulador = "";
  var cliente = $('#id_cliente').val();
  var clienteArray = cliente.split('|');
  $('.abonos').each(function (i, obj) {
    var concepto = $('#numero_fact' + i).val();
    var abono = parseFloat($('#abono_a' + i).val());
    if (abono > 0) {
      acumulador += concepto + "  ";
    }
  });
  $('#autollenar').val(clienteArray[5] + " Cancela FV: " + acumulador);
}
function regresar(ruta) {
  Swal.fire({
    title: '¿Desea regresar a la pantalla anterior?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = $("#base_url").val() + ruta;
    }
  });
}
function AnularCompIngreso(id) {
  Swal.fire({
    title: '¿Desea Anular este comprobante?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      testcompIngreso(id);
    }
  });
}
async function testcompIngreso(id) {
  try {
    const {
      value: text
    } = await Swal.fire({
      input: 'textarea',
      inputPlaceholder: 'Ingrese motivo de anulación...',
      inputAttributes: {
        'aria-label': 'Ingrese motivo de anulación'
      },
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Anular'
    })

    if (text) {
      $.ajax({
        url: "v3/" + $('#controlador').val() + "/anularCompIngreso",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          'observacion': text

        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, '', '', '', 'tabla');
        },
        error: function (json) {
          console.log(json);
        },
      });
    }
  } catch (err) {
    console.log(err);
  }
}

function mostrarPDFCompIngreso(id) {
  window.open("v3/" + $("#controlador").val() + "/mostrarPdfCompIngreso?id=" + id, "_blank");
}
function AnularCruceValores(id) {
  Swal.fire({
    title: '¿Desea Anular este cruce?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      testCruceValores(id);
    }
  });
}
async function testCruceValores(id) {
  try {
    const {
      value: text
    } = await Swal.fire({
      input: 'textarea',
      inputPlaceholder: 'Ingrese motivo de anulación...',
      inputAttributes: {
        'aria-label': 'Ingrese motivo de anulación'
      },
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Anular'
    })

    if (text) {
      $.ajax({
        url: "v3/" + $('#controlador').val() + "/anularCruceValores",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          'observacion': text

        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, '', '', '', 'tabla');
        },
        error: function (json) {
          console.log(json);
        },
      });
    }
  } catch (err) {
    console.log(err);
  }
}
function buscarRetencionesClientes() {
  $('#tableRetenciones').dataTable().fnClearTable();
  $('#tableRetenciones').dataTable().fnDestroy();
  $("#tableRetenciones").DataTable({
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
    order: [[0, 'desc']],
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
      data: {
        d: $("#desde").val(),
        h: $("#hasta").val()
      },
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      error(error) {
        console.log(error);
      },
    },
    columns: [
      {
        className: "text-center",
        data: "id",
      },
      {
        className: "text-center",
        data: "fecha",
      },
      {
        className: "text-center",
        data: "id_asiento_cabecera",
        render: function (data, type, row) {
          return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
        }
      }, {
        data: "cliente",
        render: function (data, type, row) {
          return row.id_cliente + ' | ' + row.cliente;
        }
      },
      {
        data: "descripcion",
      },
      {
        className: "text-center",
        data: "id_venta",
      },
      {
        className: "text-center",
        data: "nro_comprobante",
      },
      {
        className: "text-right",
        data: "valor_fuente",
      },
      {
        className: "text-right",
        data: "valor_iva",
      },
      {
        className: "text-right",
        data: "total",
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {
          var cadena = row.ucnombre;
          var letra = cadena.charAt(0);
          return letra + ". " + row.ucapellido;
        },
      },
      {
        className: "text-center",
        data: "estado",
        render: function (data, type, row) {
          var estado = '<label class="text-success">' + $('#activo').val() + '</label>';
          if (row.estado == 0) {
            estado = '<label class="text-danger">' + $('#inactivo').val() + '</label>';
          }
          return estado;
        },
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {
          var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> ' + $("#anulado").val();
          if (row.estado == 1) {
            accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#anular").val();
          }
          var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' + row.id + "','" + row.nro_comprobante + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','niniguna');\">" + accion + "</a></div></div>";
          return botones;
        },
      },
    ],
  });
}
function AnularClienteRetencion(id) {
  Swal.fire({
    title: '¿Desea Anular esta retención?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      testRetencionAnular(id);
    }
  });
}
async function testRetencionAnular(id) {
  try {
    const {
      value: text
    } = await Swal.fire({
      input: 'textarea',
      inputPlaceholder: 'Ingrese motivo de anulación...',
      inputAttributes: {
        'aria-label': 'Ingrese motivo de anulación'
      },
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Anular'
    })
    if (text) {
      $.ajax({
        url: "v3/" + $('#controlador').val() + "/cambiarEstadoRetencion",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          'observacion': text
        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, '', '', '', 'tabla');
        },
        error: function (json) {
          console.log(json);
        },
      });
    }
  } catch (err) {
    console.log(err);
  }
}

$("#btnguardarCompIngresovarios").click(function () {
  var validacion = validar_totalciv();
  var regex = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|1\d|2\d|3[01])$/;
  if (!regex.test($("#fecha").val())) {
    return mensajes("warning", "Ingrese una fecha valida");
  } else if ($("#sucursal").val() == "") {
    return mensajes("warning", "Seleccione el establecimiento");
  } else if ($("#concepto").val() <= 0) {
    return mensajes("warning", "Ingrese el concepto");
  } else if ($("#id_banco").val() == "") {
    return mensajes("warning", "Seleccione una Caja/Banco");
  } else if ($("#divisas").val() == "") {
    return mensajes("warning", "Seleccione la divisa");
  } else if (aux_filaCompIngresoVarios <= 0) {
    return mensajes("warning", "Agregue el detalle de valores recibidos");
  } else if (filaCruce < 0) {
    return mensajes("warning", "Agregue el detalle de complemento contable");
  } else {
    if (aux_filaCompIngresoVarios > 0) {
      let validacionTipoPago = false;
      let validacionValor = false;
      $("select[name='id_pago[]']").each((i, v) => {
        if (v.value == "") {
          validacionTipoPago = true;
        }
        if ($("#valor" + i).val() == 0) {
          validacionValor = true;
        }
      });
      if (validacionTipoPago) {
        return mensajes("warning", "Debe ingresar el tipo de pago");
      }
      if (validacionValor) {
        return mensajes("warning", "Debe ingresar el valor");
      }
    }

    if (aux_filaCruceCiv > 0) {
      var validacionCuenta = false;
      var validacionValores = false;

      $("select[name='cuenta[]']").each((i, v) => {
        if (v.value == "") {
          validacionCuenta = true;
        }
        if ($("#haber_" + i).val() === "0.00") {
          validacionValores = true;
        }
      });
      if (validacionCuenta) {
        return mensajes("warning", "Debe ingresar la cuenta");
      }
      if (validacionValores) {
        return mensajes("warning", "Ingrese el valor del haber");
      }
    }
    if ($("#total_ingresos").val() != validacion) {
      return mensajes("warning", "El valor de ingresos es mayor que en el haber del complemento contable.");
    }
    mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/clientes/nuevo_compIngresoVarios');
  }
});

function validar_totalciv() {
  var total = 0;
  $("input[name='haber[]']").each(function () {
    var valor = parseFloat($(this).val());
    if (!isNaN(valor)) {
      total += valor;
    }
  });
  total = parseFloat(total).toFixed(2, 2)
  $("#valor_base").val(total);

  return total;
}

function envalor_base(id) {
  var haber = parseFloat($("#haber_" + id).val());
  var validacion = agregar_valor(id);
  if (haber == "" || isNaN(haber) || haber <= 0 || validacion == 'error') {
    haber = 0;
  }
  $("#haber_" + id).val(parseFloat(haber).toFixed(2));
}

function agregar_valor(id) {
  e = 0;
  var total_ingresos = parseFloat($("#total_ingresos").val());
  var validacion = validar_totalciv();
  if (validacion > total_ingresos) {
    mensajes("warning", "El valor supera al monto total");
    $("#haber" + id).val(e.toFixed(2));
    return 'error';
  }
}

function verCompIngresoVarios(id_compIngreso) {
  limpiarModal();
  $("#titulo_modal_comIngVar").html('<i class="fas fa-eye"></i> Visualizador comprobante de ingreso varios');
  var data = {
    id: id_compIngreso,
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getCompIngresoVarios", data, 'json');
  if (json.responseJSON != null) {
    json = json.responseJSON;
    $("#id").val(json.id);
    $("#numero").val(json.secuencia);
    $("#asiento").val(json.id_asiento_cabecera);
    $("#fecha").val(json.fecha);
    $("#concepto").val(json.concepto);
    $("#id_banco").val(json.nombre_caja);
    $("#divisas").val(json.divisas);
    $("#total").val(json.total_ingreso);
    $("#nota").val(json.comentarios);
    $("#detalleValoresRecibidosciv").empty();
    var detalle = {
      id: id_compIngreso,
    };
    var detalleJson = ajax("v3/" + $("#controlador").val() + "/getDetalleValorRecibidoCompVarios", detalle, 'json');
    if (detalleJson.responseJSON != null) {
      detalleJson = detalleJson.responseJSON;
      var tablaHtml = '';
      for (var i = 0; i < detalleJson.length; i++) {
        tablaHtml += '<tr><td style="width: 11%; text-align: center;">' + detalleJson[i].tipo_pago + '</td>';
        tablaHtml += '<td style="width: 8%; text-align: center;">' + detalleJson[i].fecha + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: center;">' + (detalleJson[i].numero !== null ? detalleJson[i].numero : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 15%; text-align: center;">' + (detalleJson[i].banco !== null ? detalleJson[i].banco : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 14%; text-align: center;">' + (detalleJson[i].tarjeta !== null ? detalleJson[i].tarjeta : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: center;">' + (detalleJson[i].cuenta !== null ? detalleJson[i].cuenta : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 13%;">' + (detalleJson[i].girador !== null ? detalleJson[i].girador : '&nbsp;') + '</td>';
        tablaHtml += '<td style="width: 10%; text-align: right;">' + detalleJson[i].total + '</td>';
        tablaHtml += '</tr>';
      }
      $("#detalleValoresRecibidosciv").html(tablaHtml);
    }
    var data = {
      id: id_compIngreso,
    };
    var detalleJson1 = ajax("v3/" + $("#controlador").val() + "/getDetalleCompVarios", data, 'json');
    if (detalleJson1.responseJSON != null) {
      detalleJson1 = detalleJson1.responseJSON;
      var tablaHtml = '';
      for (var j = 0; j < detalleJson1.length; j++) {
        tablaHtml += '<tr><td style="width: 50%;">' + detalleJson1[j].codigo + ' | ' + detalleJson1[j].nombre + '</td>';
        tablaHtml += '<td style="width: 15%;"> $ </td>';
        tablaHtml += '<td style="width: 15%; text-align: right;">0.00</td>';
        tablaHtml += '<td style="width: 10%; text-align: right;">' + detalleJson1[j].haber + '</td>';
        tablaHtml += '</tr>';
      }
      $("#detalleDeudaCliente").html(tablaHtml);
    }
    $("#myModal").modal("toggle");
  }
}

function AnularCompIngresoVarios(id) {
  Swal.fire({
    title: '¿Desea Anular este comprobante?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      testcompIngresoVarios(id);
    }
  });
}
async function testcompIngresoVarios(id) {
  try {
    const {
      value: text
    } = await Swal.fire({
      input: 'textarea',
      inputPlaceholder: 'Ingrese motivo de anulación...',
      inputAttributes: {
        'aria-label': 'Ingrese motivo de anulación'
      },
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Anular'
    })

    if (text) {
      $.ajax({
        url: "v3/" + $('#controlador').val() + "/anularCompIngresoVarios",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          'observacion': text

        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, '', '', '', 'tabla');
        },
        error: function (json) {
          console.log(json);
        },
      });
    }
  } catch (err) {
    console.log(err);
  }
}

function mostrarPDFCompIngresoVarios(id) {
  window.open("v3/" + $("#controlador").val() + "/mostrarPdfCompIngresoVarios?id=" + id, "_blank");
}

function cargarFacturas(nombreCliente) {
  var json = ajax("v3/" + $("#controlador").val() + "/getComprobanteCliente", { nombreCliente }, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    console.log(json.length);
    if (json.length > 0) {
      $("#facturas").empty();
      $("#facturas").append("<option value=''>--Seleccione--</option>");
      json.forEach(function (index) {
        let option = document.createElement('option');
        option.text = index.text;
        option.value = index.id;
        $("#facturas").append(option);
      });
    }
  } else {
    $("#facturas").empty();
  }
}

function cargarTablaCliente() {
  let idFactura = $("#facturas").val();
  console.log(idFactura);
  var json = ajax("v3/" + $("#controlador").val() + "/newData2Cliente", { idFactura }, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    $("#detalleNotaCredito").empty();
    $("#detalleNotaCredito").append(json.table);
    $('#total_final').val(parseFloat(json.total).toFixed(2, 2));
    document.getElementById('valor_contable').value = parseFloat(json.valor_contable).toFixed(2, 2)
    sumaGlobal()
  } else {
    $("#tablaNotaCredito").empty();
  }
}


function adder(e) {
  let cantidad = 0;
  let precio = 0;
  let total = 0;
  $('.cantidades').each(function (index) {
    let check = $("#verificar" + index).prop('checked');
    let nodata = $('#ocultosp' + index);
    if (check) {
      $(this).parent().parent().find('.vercheckbox').val('1');
      nodata.removeClass('ocultos');
      cantidad = parseFloat($(this).val());
      precio = parseFloat($(this).parent().find('.precios').val());
      total += cantidad * precio;
    } else {
      $(this).parent().parent().find('.vercheckbox').val('0');
      if ($('.ocultosp').hasClass('.ocultos')) {
      } else {
        nodata.addClass('ocultos');
      }
    }
  });
  sumaGlobal();
}

function sumaGlobal() {
  let cheks = document.querySelectorAll('.checkProducto');
  let precios = document.querySelectorAll('.precios');
  let valorImpuesto = document.querySelectorAll('.iva_producto')
  let valorDescuento = document.querySelectorAll('.descuento')
  let cantidad = document.querySelectorAll('.cantidad')
  let subtotal0 = 0;
  let subtotal12 = 0;
  let impuesto = 0;
  let descuento = 0;
  let mostrarSubtotal0 = document.getElementById('subtotal0')
  let mostrarSubtotal12 = document.getElementById('subtotal12')
  let mostrarSubtotal = document.getElementById('subtotal')
  let mostrarImpuesto = document.getElementById('impuesto')
  let mostrarDescuento = document.getElementById('descuento')
  let mostrarTotal = document.getElementById('total')
  for (let i = 0; i < cheks.length; i++) {

    if (cheks[i].value == 1) {  //tiene iva
      console.log(valorImpuesto[i].value);
      if (valorImpuesto[i].value > 0) {
        console.log(valorImpuesto[i].value)
        subtotal12 += parseFloat(precios[i].value) * cantidad[i].value;
        impuesto += parseFloat(valorImpuesto[i].value);
      } else {
        subtotal0 += parseFloat(precios[i].value) * cantidad[i].value;
      }
      descuento += parseFloat(valorDescuento[i].value);
    }
  }
  mostrarSubtotal0.value = parseFloat(subtotal0).toFixed(2);
  mostrarSubtotal12.value = parseFloat(subtotal12).toFixed(2);
  mostrarDescuento.value = parseFloat(descuento).toFixed(2);
  mostrarSubtotal.value = parseFloat((subtotal0 + subtotal12)).toFixed(2);
  mostrarImpuesto.value = parseFloat(impuesto).toFixed(2);
  mostrarTotal.value = (parseFloat(parseFloat(subtotal0 + subtotal12) - parseFloat(descuento)) + parseFloat(impuesto)).toFixed(2);
}


const validarFormCliente = () => {
  let formulario = document.forms["frm_notacreditocliente"];

  let array = [];
  if (formulario.sucursal.value == "") {
    array.push("sucursal");
  } else if (formulario.punto_emision.value == "") {
    array.push("punto emisión");
  } else if (formulario.fecha.value == "") {
    array.push("fecha");
  } else if (formulario.concepto.value == "") {
    array.push("concepto");
  } else if (formulario.cliente.value == "") {
    array.push("cliente");
  } else if (formulario.facturas.value == "") {
    array.push("facturas");
  }
  return array;
};

function guardarNotaCreditoCliente() {

  let valForm = validarFormCliente();
  if (valForm.length == 0) {
    mensajes("question", $("#pregunta").val(), "frm_notacreditocliente", "action", "v3/clientes/notadecredito");
  } else {
    return mensajes("warning", "Ingrese información del campo " + valForm.join(","));
  }

}

function obtener_caja() {
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/obtenerPuntoEmision",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    datatype: "json",
    data: {
      id: $("#sucursal").val(),
    },
    success: function (data) {
      if ($("#sucursal").val() != "") {
        $("#punto_emision").empty();
        $("#punto_emision").append('<option value="">--Seleccione--</option>');
        $.each(data, function (key, registro) {
          $("#punto_emision").append(
            "<option value=" +
            registro.id +
            ">" +
            registro.codigo_sucursal +
            "-" +
            registro.codigo_caja +
            "</option>"
          );
        });
      } else {
        $("#punto_emision").empty();
        $("#punto_emision").append('<option value="">--Seleccione--</option>');
      }
    },
    error: function (data) {
      console.log(data);
    },
  });
}

