var fila = 0;
var filaImpuesto = 0;
var btnRuc = $(".ladda-button-demo-ruc").ladda();
var btnCed = $(".ladda-button-demo-ced").ladda();
var btnCer = $(".ladda-button-demo-cer").ladda();
$(document).ready(function () {
  let id_proveedor = $("#editar_comprobante").val();
  if (id_proveedor !== "") {
    $("#forma_pago").val($("#editar_forma_pago").val()).trigger("change");
    $("#sucursal").val($("#editar_sucursal").val()).trigger("change");
    $("#divisas").val(1).trigger("change");
    $("#banco").val($("#editar_banco").val()).trigger("change");
    $("#num_cheque").val($("#editar_numerocheque").val());
    $("#fecha_cheque").val($("#editar_fechacheque").val());
    $("#valor").val($("#editar_valor").val());
    $("#nombre_acreedores").select2({
      disabled: true,
    });
    $("#direccion").prop("readonly", true);
    $("#girado").prop("readonly", true);
    $("#fecha").prop("readonly", true);
    $("#banco").select2({
      disabled: true,
    });
    $("#divisas").select2({
      disabled: true,
    });
    $("#fecha_cheque").prop("readonly", true);
    $("#num_cheque").prop("readonly", true);
    $("#valor").prop("readonly", true);
    $("#forma_pago").select2({
      disabled: true,
    });
    $("#sucursal").select2({
      disabled: true,
    });
    $.ajax({
      type: "post",
      url: "v3/" + $("#controlador").val() + "/buscarNombreProveedor",
      headers: {
        "X-CSRF-TOKEN": $("input[name=_token]").val(),
      },
      datatype: "json",
      data: {
        id_empresa: id_proveedor,
      },
      success: function (data) {
        if (data.value != "no") {
          $("#ruc").val(data.value);
          $("#direccion").val(data.direccion);
          let option = new Option(
            data.nombreProveedor,
            data.idProveedor,
            true,
            true
          );
          $("#nombre_acreedores").append(option).trigger("change");
        } else {
          //$("#ruc").prop("readonly", false);
        }
      },
      error: function (data) {
        mensajes("question", $("#pregunta").val());
      },
    });
  }
  $("#elementTour").val("hero-agregarProveedor");
  console.log("asdas");
  if ($("#submodulo_").val() == "nuevaretencion") {
    $(".botonMenu").trigger("click");
  }
  btnRuc.click(function () {
    btnRuc.ladda("start");
  });
  btnCed.click(function () {
    btnCed.ladda("start");
  });
  btnCer.click(function () {
    btnCer.ladda("start");
  });
  try {
    $("#tableProveedores").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
      iDisplayLength: 25,
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
          className: "text-center",
          data: "id",
        },
        {
          data: "razonsocial",
        },
        {
          data: "nombrecomercial",
        },
        {
          data: "email",
        },
        {
          className: "text-center",
          data: "tipo",
        },
        {
          className: "text-center",
          data: "logo",
          render: function (type, data, row) {
            var logo = "";
            if (row.logo !== null) {
              logo =
                '<img src="../storage/app/logo/' +
                row.logo +
                '" style="width: 100px" onclick="mostrarImg(\'../storage/app/logo/' +
                row.logo +
                "')\"/>";
            }
            return logo;
          },
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
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> ' +
                $("#desactivar").val();
            }
            var botones =
              '<div class="btn-group"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProveedor(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' +
              row.id +
              "','" +
              row.estado +
              "','Proveedor');\">" +
              accion +
              "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableRetenciones").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
          data: "id",
        },
        {
          className: "text-center",
          data: "fecha",
        },
        {
          data: "id_proveedor",
        },
        {
          data: "descripcion",
        },
        {
          className: "text-center",
          data: "id_compra",
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
          data: "electronica",
          render: function (data, type, row) {
            var electronica = row.electronica == 1 ? "Si" : "No";
            return electronica;
          },
        },
        {
          className: "text-center",
          data: "doc_electronico",
          render: function (data, type, row) {
            var estado =
              '<img src="' +
              $("#base_url").val() +
              '/v3/dist/img/puntoamarillo.png" style="width:15px;" title="Sin Autorizar" />';
            if (row.doc_electronico == 5) {
              estado =
                '<img src="' +
                $("#base_url").val() +
                '/v3/dist/img/puntoverde.png" style="width:20px;" title="Autorizado" />';
            } else if (row.doc_electronico == 3) {
              estado =
                '<img src="' +
                $("#base_url").val() +
                '/v3/dist/img/puntoplomo.png" style="width:20px;" title="Autorizado" />';
            }
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> ' +
                $("#desactivar").val();
            }
            var botones =
              '<div class="btn-group"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' +
              row.id +
              "','" +
              row.nro_comprobante +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' +
              row.id +
              "','" +
              row.estado +
              "','Retenciones');\">" +
              accion +
              "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableSaldosIniciales").DataTable({
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
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
          data: "id",
        },
        {
          data: "observacion",
        },
        {
          className: "text-center",
          data: "fecha",
        },
        {
          data: "proveedor",
        },
        {
          className: "text-right",
          data: "total_final",
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
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProveedor(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','SaldosIniciales');\">" + accion + "</a></div></div>";
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
            var botones =
              '<div class="btn-group"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRubros(\'' +
              row.codigo +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' +
              row.codigo +
              "','" +
              row.estado +
              "','Rubros');\">" +
              accion +
              "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableComprobanteEgreso").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
        url: "v3/" + $("#controlador").val() + "/" + "allComprobantes",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          d: $("#desde").val(),
          h: $("#hasta").val(),
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
          data: "id_asiento_cabecera",
        },
        {
          className: "text-center",
          data: "secuencia",
        },
        {
          className: "text-center",
          data: "fecha_cheque",
        },
        {
          className: "text-right",
          data: "no_cheque",
        },
        {
          data: "razonsocial",
        },
        {
          data: "descripcion",
        },
        {
          className: "text-right",
          data: "valor_pago",
          render: function (data, type, row) {
            let valor_pago = "";
            if (row.valor_pago > 0) {
              valor_pago = row.valor_pago.toLocaleString("en-US", {
                style: "currency",
                currency: "USD",
              });
            }
            return valor_pago;
          },
        },
        {
          className: "text-center",
          data: "nombre_crea",
        },
        {
          className: "text-center",
          data: "nombre_modi",
          render: function (data, type, row) {
            let estado = "";
            if (row.estado == 0) {
              estado = row.nombre_modi;
            }
            return estado;
          },
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado =
              row.estado == 1
                ? '<label class="text-success">' + $('#activo').val() + '</label>'
                : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
            return estado;
          },
        },

        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> ' +
                $("#inactivar").val();
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verComprobante(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              (row.estado != 0
                ? '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' +
                row.id +
                "','" +
                row.estado +
                "','ComprobanteEgreso');\">" +
                accion
                : "") +
              "</a><a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDF('" +
              row.id +
              "');\"><i class='fas fa-file-pdf' style='color:gray;'></i> PDF</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableNotadecredito").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
          data: "id",
        },
        {
          className: "text-center",
          data: "fecha",
        },
        {
          className: "text-center",
          data: "secuencia_factura",
        },
        {
          data: "id_asiento_cabecera",
        },
        {
          data: "concepto",
        },
        {
          data: "nombrecomercial",
        },
        {
          className: "text-rigth",
          data: "valor_contable",
        },
        {
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
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> ' +
                $("#desactivar").val();
            }
            var botones =
              '<div class="btn-group"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRubros(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' +
              row.id +
              "','" +
              row.estado +
              "','Rubros');\">" +
              accion +
              "</a></div></div>";
            return botones;
          },
        },
      ],
    });



    $("#tableCruceValor").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
        url: "v3/" + $("#controlador").val() + "/" + "allCruce",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          d: $("#desde").val(),
          h: $("#hasta").val(),
        },
        error(error) {
          console.log(error);
        },
      },
      columns: [
        {
          className: "text-center",
          data: "secuencia",
        },
        {
          className: "text-left",
          data: "fecha_pago",
        },
        {
          className: "text-center",
          data: "id_asiento_cabecera",
        },
        {
          className: "text-center",
          data: "nombrecomercial",
        },
        {
          className: "text-left",
          data: "detalle",
        },
        {
          className: "text-right",
          data: "total_disponible",
        },
        {
          data: "nombre_crea",
        },
        {
          data: "nombre_modi",
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
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> Inactivar';
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCruce(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              (row.estado != 0
                ? '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="preguntarCambioEstadoCruce(\'' +
                row.id +
                "','" +
                row.estado +
                "','CruceValor');\">" +
                accion
                : "") +
              "</a><a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFCruce('" +
              row.id +
              "');\"><i class='fas fa-file-pdf' style='color:gray;'></i> PDF</a></div></div>";
            return botones;
          },
        },
      ],
    });
  } catch (error) {
    console.log(error);
  }
  $("#lblRuc").click(function (e) {
    if ($("#id").val() == "") {
      e.preventDefault();
      btnRuc.ladda("stop");
      return mensajes("warning", "Debe ingresar el ruc del proveedor a crear...");
    }
  });
  $("#proveedorRuc").change(function (e) {
    if (($("#proveedorRuc").val().length = 0)) {
      btnRuc.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("proveedorRuc");
      var file = inputFileImage.files[0];
      var fileSize = $("#proveedorRuc")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnRuc.ladda("stop");
        $("#proveedorRuc").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("proveedorRuc", file);
        fd.append("tipo_archivo", "proveedorRuc");
        fd.append("id_proveedor", $("#id").val());
        $.ajax({
          url: "v3/utilidades/guardarArchivo",
          headers: {
            "X-CSRF-TOKEN": $("#token").val(),
          },
          data: fd,
          processData: false,
          contentType: false,
          type: "post",
          success: function (data) {
            var imagen = data.split("|");
            if (imagen[0] == "ok") {
              btnRuc.ladda("stop");
              $("#proveedorRuc").val("");
              $("#lblRuc").html(
                'Ruc: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=proveedorRuc" target="new">ver/descargar</a>'
              );
            } else {
              btnRuc.ladda("stop");
              alert(imagen[1]);
              $("#proveedorRuc").val("");
            }
          },
        });
      }
    }
  });
  $("#lblCedula").click(function (e) {
    if ($("#id").val() == "") {
      e.preventDefault();
      btnCed.ladda("stop");
      return mensajes("warning", "Debe ingresar el ruc del proveedor a crear...");
    }
  });
  $("#proveedorCedula").change(function (e) {
    if (($("#proveedorCedula").val().length = 0)) {
      btnCed.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("proveedorCedula");
      var file = inputFileImage.files[0];
      var fileSize = $("#proveedorCedula")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnCed.ladda("stop");
        $("#proveedorCedula").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("proveedorCedula", file);
        fd.append("tipo_archivo", "proveedorCedula");
        fd.append("id_proveedor", $("#id").val());
        $.ajax({
          url: "v3/utilidades/guardarArchivo",
          headers: {
            "X-CSRF-TOKEN": $("#token").val(),
          },
          data: fd,
          processData: false,
          contentType: false,
          type: "post",
          success: function (data) {
            var imagen = data.split("|");
            if (imagen[0] == "ok") {
              btnCed.ladda("stop");
              $("#proveedorCedula").val("");
              $("#lblCedula").html('Cedula: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=proveedorCedula" target="new">ver/descargar</a>');
            } else {
              btnCed.ladda("stop");
              $("#proveedorCedula").val("");
            }
          },
        });
      }
    }
  });
  $("#proveedorCedula").focus(function () {
    alert(1);
  });
  $("#lblVotacion").click(function (e) {
    if ($("#id").val() == "") {
      e.preventDefault();
      btnCer.ladda("stop");
      return mensajes(
        "warning",
        "Debe ingresar el ruc del proveedor a crear..."
      );
    }
  });
  $("#proveedorVotacion").change(function (e) {
    if (($("#proveedorVotacion").val().length = 0)) {
      btnCer.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("proveedorVotacion");
      var file = inputFileImage.files[0];
      var fileSize = $("#proveedorVotacion")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnCer.ladda("stop");
        $("#proveedorVotacion").val("");
        alert("Error: imagen muy grande o tiene una resolucion muy alta");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("proveedorVotacion", file);
        fd.append("tipo_archivo", "proveedorVotacion");
        fd.append("id_proveedor", $("#id").val());
        $.ajax({
          url: "v3/utilidades/guardarArchivo",
          headers: {
            "X-CSRF-TOKEN": $("#token").val(),
          },
          data: fd,
          processData: false,
          contentType: false,
          type: "post",
          success: function (data) {
            var imagen = data.split("|");
            if (imagen[0] == "ok") {
              btnCer.ladda("stop");
              $("#proveedorVotacion").val("");
              $("#lblVotacion").html(
                'Cartificado de votación: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=proveedorVotacion" target="new">ver/descargar</a>'
              );
            } else {
              btnCer.ladda("stop");
              $("#proveedorVotacion").val("");
            }
          },
        });
      }
    }
  });
  $("#proveedorVotacion").focus(function () {
    alert(1);
  });
  $("#id_cuentas").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--" + $("#seleccione").val() + "--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getAllCuentas",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (response) {
        return {
          results: response,
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $("#id_configuracion").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--" + $("#seleccione").val() + "--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getAllBancos",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (response) {
        return {
          results: response,
        };
      },
      error() {
        window.open("v3/admin", "_self");
      },
      cache: true,
    },
  });
  $("#btnGuardarProveedor").click(function () {
    if ($("#tipo").val() == "") {
      return mensajes("warning", $("#alertTipoIdentificacion").val() + "...");
    } else if ($("#id").val() == "") {
      return mensajes("warning", $("#alertNumeroIdentificacion").val() + "...");
    } else if ($("#razonsocial").val() == "") {
      return mensajes("warning", $("#alertRazonSoc").val() + "...");
    } else if ($("#nombrecomercial").val() == "") {
      return mensajes("warning", $("#alertNomComercial").val() + "...");
    } else if ($("#email").val() == "") {
      return mensajes("warning", $("#alertCorreo").val() + "...");
    } else if ($("#telefono1").val() == "") {
      return mensajes("warning", $("#alertConvencional").val() + "...");
    } else if ($("#telefono2").val() == "") {
      return mensajes("warning", $("#alertCelular").val() + "...");
    } else if ($("#direccion").val() == "") {
      return mensajes("warning", $("#alertDireccion").val() + "...");
    } else if ($("#id_ciudad").val() == "") {
      return mensajes("warning", $("#alertCiudad").val() + "...");
    } else {
      if ($("#tipo").val() == 4 && $("#id").val().length !== 13) {
        return mensajes("warning", $("#alertLongIdentificacion").val() + "...");
      }
      if ($("#tipo").val() == 5 && $("#id").val().length !== 10) {
        return mensajes("warning", $("#alertLongIdentificacion").val() + "...");
      }
      var seleccionado = $("#id_ciudad option:selected").text();
      $("#ciudad").val(seleccionado);
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#debe").select2({
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
  $("#haber").select2({
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
  $("#btnGuardarRubros").click(function () {
    if ($("#codigo").val() == "") {
      mensajes("info", "Ingrese el codigo del rubro");
    } else if ($("#nombre").val() == "") {
      mensajes("info", "Ingrese el nombre del rubro");
    } else if ($("#debe").val() == "") {
      mensajes("info", "Seleccione la cuenta debe del rubro");
    } else if ($("#haber").val() == "") {
      mensajes("info", "Seleccione la cuenta haber del rubro");
    } else if ($("#tipo").val() == "") {
      mensajes("info", "Seleccione el tipo del rubro");
    } else {
      if ($("#codigo").val() != "") {
        validarCodigo();
      }
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#id_proveedor").select2({
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
        "X-CSRF-TOKEN": $("#token").val(),
      },
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (response) {
        return {
          results: response,
        };
      },
      error(error) { },
      cache: true,
    },
  });
  $("#agregarLinea").on("click", function () {
    var html = '<tr id="tr_' + fila + '">';
    html +=
      '<td><input type="text" class="form-control" id="rubro_' +
      fila +
      '" name="rubro[]" value="" /></td>';
    html +=
      '<td><input type="text" class="form-control" id="detalle_' +
      fila +
      '" name="detalle[]" value="" " /></td>';
    html +=
      '<td><input type="text" class="form-control" id="divisa_' +
      fila +
      '" name="divisa[]" value="USD" readonly /></td>';
    html +=
      '<td><input type="text" class="form-control text-right" id="valor_' +
      fila +
      '" name="valor[]" value="0.00" "/></td>';
    html +=
      '<td><input type="text" class="form-control text-right" id="total_base_' +
      fila +
      '" name="total_base[]" value="0.00" "/></td>';
    html +=
      '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(\'' +
      fila +
      "')\"></i></td>";
    html += "</tr>";
    $("#detalleSaldosIniciales").before(html);
    fila++;
  });
  $("#btnguardarRetencion").click(function () {
    if ($("#facturas").val() == "") {
      return mensajes("info", "Debe seleccionar una factura");
    } else if ($("#establecimiento").val() == "" || $("#emision").val() == "") {
      return mensajes(
        "info",
        "Seleccione el establecimiento y el punto de emisión de la retención"
      );
    } else if ($("#descripcion").val() == "") {
      return mensajes("warning", "Debe ingresar un concepto para la retención");
    } else if ($("#total").val() == 0) {
      return mensajes("info", "Debe agregar un valor para la retención");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });

  // 1912024

  $("#nombre_acreedores").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--" + $("#seleccione").val() + "--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarAcreedores",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (response) {
        return {
          results: response,
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });

  $("#nombre_acreedores").on("change", function () {
    let res = $("#nombre_acreedores option:selected").text();
    let rucEmpresa = res.split("|").map(function (value) {
      return value.trim();
    });

    $.ajax({
      type: "post",
      url: "v3/" + $("#controlador").val() + "/buscarNombreProveedor",
      headers: {
        "X-CSRF-TOKEN": $("input[name=_token]").val(),
      },
      datatype: "json",
      data: {
        id_empresa: rucEmpresa[0],
      },
      success: function (data) {
        if (data.value != "no") {
          $("#ruc").val(data.value);
          $("#direccion").val(data.direccion);
          buscar_proveedor();
        } else {
          //$("#ruc").prop("readonly", false);
        }
      },
      error: function (data) {
        mensajes("question", $("#pregunta").val());
      },
    });
  });
});

function habilitarCampo(pos) {
  let resultado = $("#valorasumir" + pos).prop("readOnly");
  $("#valorasumir" + pos).prop("readonly", !resultado);
  $("#valorasumir" + pos).val(resultado ? $("#retencion" + pos).val() : "0.00");
  $("#valorasumir" + pos).prop("disabled", true);
}

function addNewRow(
  total_final,
  pos,
  fecha,
  valor,
  factura,
  sucursal,
  fact_numero,
  observacion,
  retencion,
  id
) {
  return (
    "<tr>" +
    "<td> <input class='form-control' type='text'  id='vence" +
    pos +
    "' readonly='' value='" +
    fecha +
    "'> </td>" +
    "<input class='form-control' type='hidden' name='id_actualiza" +
    pos +
    "' id='id_actualiza" +
    pos +
    "' value='" +
    id +
    "'>" +
    "<td> <input class='form-control' type='text'  id='tipo" +
    pos +
    "' value='" +
    factura +
    "' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' name='numero" +
    pos +
    "' id='numero" +
    pos +
    "' value='" +
    fact_numero +
    "' readonly=''> </td>" +
    "<td> <input class='form-control'  type='text' name='concepto" +
    pos +
    "' id='concepto" +
    pos +
    "' value='Fact #:" +
    fact_numero +
    " Prov: " +
    observacion +
    "' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' style='text-align: right;'  id='total" +
    pos +
    "' readonly='' value='" +
    total_final +
    "'> </td>" +
    "<td> <input class='form-control' type='text' style='text-align: right;'  id='retencion" +
    pos +
    "' readonly='' value='" +
    (retencion !== undefined && retencion !== null ? retencion : "0.00") +
    "'> </td>" +
    "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;text-align: right; ' name='saldo" +
    pos +
    "' value='" +
    valor +
    "' id='saldo" +
    pos +
    "' readonly=''> </td>" +
    "<td> <input class='form-control abonos' type='text' style='background-color: #c9ffe5; text-align: right;' name='abono" +
    pos +
    "' id='abono" +
    pos +
    "' onchange='validar_td(" +
    pos +
    ")' value='0.00'></td>" +
    "<td> <input class='form-control' type='text' style=' text-align: right;' value='" +
    valor +
    "' id='nuevo_saldo" +
    pos +
    "' readonly=''></td>" +
    "<td> <input class='form-control' type='text' name='valorasumir" +
    pos +
    "' value='0.00' style='text-align: right;' id='valorasumir" +
    pos +
    "' readonly></td>" +
    "<td style='text-align:center'> <input type='checkbox'  id='retencionIn" +
    pos +
    "' onclick='habilitarCampo(" +
    pos +
    ")'  /></td>" +
    "</tr>"
  );
}

function onlyNumberKey(evt) {
  let ASCIICode = evt.which ? evt.which : evt.keyCode;
  if ((ASCIICode >= 48 && ASCIICode <= 57) || ASCIICode === 46) {
    if (evt.target.value.indexOf(".") !== -1 && ASCIICode === 46) {
      return false;
    }
    return true;
  }
  return false;
}

function suma_totales2() {
  var total = 0;
  $(".abonos").each(function (i, obj) {
    total += parseFloat($(this).val());
  });
  $("#total_suma").val(total.toFixed(2, 2));
  var cheque = $("#valor").val();
  var restante = cheque - total;
  if (restante == "" || isNaN(restante) || restante < 0) {
    restante = 0;
  }
  $("#saldo_favor").val(restante.toFixed(2, 2));
}

function aplicarDeuda() {
  let valor = Number(document.getElementById("valor").value);
  var valor2 = parseFloat($("#valor").val());
  var valor_saldo = 0;
  var total = 0;
  var cero = 0;

  $(".abonos").each(function (i, obj) {
    saldo += parseFloat($("#saldo" + i).val());
    valor_saldo = parseFloat($("#saldo" + i).val());
    valor -= valor_saldo;
    var cont = parseFloat($("#abono" + i).val());
    if (isNaN(cont)) {
      cont = 0;
    }
    if (valor > valor_saldo) {
      $("#abono" + i).val(valor_saldo.toFixed(2, 2));
      $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
    } else {
      total = valor + valor_saldo;
      if (total <= valor2) {
        if (total > 0) {
          if (total > valor_saldo) {
            total = valor_saldo;
            $("#abono" + i).val(total.toFixed(2, 2));
            $("#nuevo_saldo" + i).val(cero.toFixed(2, 2));
          } else {
            $("#abono" + i).val(total.toFixed(2, 2));
            var saldo = parseFloat($("#saldo" + i).val()) - total;
            $("#nuevo_saldo" + i).val(saldo.toFixed(2, 2));
          }
        }
      }
    }
  });

  var total = 0;
  if (!isNaN(valor) && !isNaN(valor_saldo)) {
    $("#verificar_superavit").val(1);
  } else {
    $("#verificar_superavit").val(0);
    return mensajes("warning", "Ingrese el valor");
  }
  suma_totales2();
}

function buscar_proveedor() {
  let id_proveedor = $("#ruc").val();
  anterioresAnticipos();
  $("#girado").val(id_proveedor);
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/buscarProveedor",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      id_proveedor: id_proveedor,
    },
    success: function (data) {
      if (data.value != "no resultados") {
        $("#creadetalleFacturar").empty();
        document.getElementById("contador_total").value = data[5].length;
        for (i = 0; i < data[5].length; i++) {
          if (data[5][i].tipo == 1) {
            var row = addNewRow(
              data[5][i].total_final,
              i,
              data[5][i].fecha_asiento,
              data[5][i].valor_contable,
              "COM-FA",
              data[5][i].sucursal,
              data[5][i].numero,
              data[5][i].proveedor + " " + data[5][i].observacion,
              //data[8][i],
              data[5][i].valor_nuevo,
              data[5][i].id
            );
            $("#detalleDeudasProveedor").append(row);
            fila = i;
          } else if (data[5][i].tipo == 2) {
            var row = addNewRow(
              data[5][i].total_final,
              i,
              data[5][i].fecha_asiento,
              data[5][i].valor_contable,
              "COM-FACT",
              data[5][i].sucursal,
              data[5][i].numero,
              data[5][i].proveedor + " " + data[5][i].observacion,
              // data[8][i],
              data[5][i].valor_nuevo,
              data[5][i].id
            );
            $("#detalleDeudasProveedor").append(row);
            fila = i;
          } else {
            var row = addNewRow(
              i,
              data[5][i].fecha_asiento,
              data[5][i].valor_contable,
              "SALDO INICIAL",
              data[5][i].sucursal,
              data[5][i].numero,
              data[5][i].observacion,
              "Hola",
              data[5][i].valor_nuevo,
              data[5][i].id
            );
            $("#detalleDeudasProveedor").append(row);
            fila = i;
          }
        }
      }
    },
    error: function (data) {
      mensajes("error", "Existión un error en el proceso");
    },
  });
}

function eliminarFila(fila) {
  $("#tr_" + fila).remove();
  fila--;
}
function agregarProveedor() {
  limpiarModal();
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fa-solid fa-user-tie"></i> ' + $("#nuevo").val() + " Proveedor"
  );
  $("#myModal").modal("toggle");
}
function agregarRubros() {
  limpiarModal();
  $("#codigo").removeAttr("readonly");
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fas fa-plus-square"></i> ' +
    $("#nuevo").val() +
    " " +
    $("#submodulo_").val()
  );
  $("#myModal").modal("toggle");
}
function agregarSaldoInicial() {
  window.open("v3/" + $("#controlador").val() + "/nuevo_saldoinicial", "_self");
}
function mostrarImg(url) {
  limpiarModal();
  $("#titulo_modal_img").html(
    '<i class="fa fa-picture-o"></i> Logo ' + $("#submodulo_").val()
  );
  $("#url_logo").attr("src", url);
  $("#modalImg").modal("toggle");
}
function verProveedor(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getProveedor",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#titulo_modal").html('<i class="fas fa-edit"></i> ' + $("#editar").val() + " proveedor " + capitalizar(json.nombrecomercial));
      $("#id").val(json.id);
      $("#tipo").val(json.tipo).trigger("change");
      $("#razonsocial").val(json.razonsocial);
      $("#nombrecomercial").val(json.nombrecomercial);
      $("#email").val(json.email);
      $("#email2").val(json.email2);
      $("#telefono1").val(json.telefono1);
      $("#telefono2").val(json.telefono2);
      $("#id_ciudad").val(json.id_ciudad).trigger("change");
      $("#direccion").val(json.direccion);
      $("#urbanizacion").val(json.urbanizacion);
      $("#etapa").val(json.etapa);
      $("#manzana").val(json.manzana);
      $("#villa").val(json.villa);
      $("#referencia").val(json.referencia);
      $("#id_tipoproveedor").val(json.id_tipoproveedor).trigger("change");
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/bancoCombo",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.id_configuracion,
        },
        dataType: "html",
        success: function (html) {
          $("#id_configuracion").append(html).trigger("change");
        },
      });
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/cuentaContableCombo",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.id_cuentas,
        },
        dataType: "html",
        success: function (html) {
          $("#id_cuentas").append(html).trigger("change");
        },
      });
      $("#cuenta").val(json.cuenta);
      $("#beneficiario").val(json.beneficiario);
      $("#identificacion").val(json.identificacion);
      $("#tipo_cuenta").val(json.tipo_cuenta).trigger("change");
      idPais = json.id_pais_ciudad;
      idProvincia = json.id_provincia_ciudad;
      idCiudad = json.id_ciudad;
      $("#pais").val(idPais).trigger("change");
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      if (json.ruta_ruc !== null)
        $("#lblRuc").html('RUC: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=proveedorRuc" target="new">ver/descargar</a>');
      else $("#lblRuc").html("RUC");
      if (json.ruta_cedula !== null)
        $("#lblCedula").html('Cedula: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=proveedorCedula" target="new">ver/descargar</a>');
      else $("#lblCedula").html("Cedula");
      if (json.ruta_certificado !== null)
        $("#lblVotacion").html('Certificado de votación: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=proveedorVotacion" target="new">ver/descargar</a>');
      else $("#lblVotacion").html("Certificado de votación");
      $("#myModal").modal("toggle");
    },
    error(error) {
      console.log(error);
    },
  });
}
function verRubros(codigo) {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getConfiguracionRubros",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      codigo: codigo,
    },
    dataType: "json",
    success: function (json) {
      $("#codigo").val(json.codigo);
      $("#nombre").val(json.nombre);
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/cuentaContable",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.debe,
        },
        dataType: "html",
        success: function (html) {
          $("#debe").append(html).trigger("change");
        },
      });
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/cuentaContable",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.haber,
        },
        dataType: "html",
        success: function (html) {
          $("#haber").append(html).trigger("change");
        },
      });
      $("#tipo").val(json.tipo).trigger("change");
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      $("#codigo").attr("readonly", "readonly");
      $("#titulo_modal").html(
        '<i class="fas fa-plus-square"></i> Modificar Rubro'
      );
      $("#myModal").modal("toggle");
    },
  });
}
function proveedor(id) {
  window.location.href =
    "v3/" + $("#controlador").val() + "/proveedor?id=" + id;
}
function validarCodigo() {
  if ($("#codigo").val() != "") {
    return;
  }
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/verificarRubro",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      codigo: $("#codigo").val(),
    },
    dataType: "json",
    success: function (json) {
      if (json != "") {
        if (json.status == "error") {
          return mensajes("warning", json.message);
        }
      }
    },
    error: function () { },
  });
}
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
      $("#contenidoHtml").html(html);
      $("#myModal").modal("toggle");
    },
    error() {
      window.open("v3/admin", "_self");
    },
  });
}

function agregarRetencion() {
  window.open("v3/" + $("#controlador").val() + "/nuevaretencion", "_self");
}
function agregarNotadecredito() {
  window.open("v3/" + $("#controlador").val() + "/nuevanotadecredito", "_self");
}
function obtenerFactura() {
  var d = $("#facturas").val().split("|");
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/obtenerFactura",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: d[0],
    },
    dataType: "json",
    success: function (json) {
      $("#id_factura").val(json.id);
      $("#fecha").val(json.fecha);
      $("#serie").val(json.serie);
      $("#secuencial").val(json.secuencia_factura);
      $("#tipo_comprobante").val(json.tipo_comprobante);
      $("#id_proveedor_").val(json.proveedor);
      $("#proveedor").val(json.razonsocial);
      $("#observacion").val(json.observacion);
      $("#autorizacion").val(json.autorizacion);
      $("#subtotal").val(json.subtotal);
      $("#iva_total").val(json.iva_total);
      $("#valor_contable").val(json.valor_contable);
      $("#total_final").val(json.total_final);
      agregarDetCompra();
    },
  });
}
function agregarImpuesto() {
  if ($("#facturas").val() != "") {
    filaImpuesto++;
    var html = "";
    html += '<tr id="tr_' + filaImpuesto + '">';
    html +=
      '<td><select class="form-control select2" style="width: 100%;" id="tipo_retencion' +
      filaImpuesto +
      '" name="tipo_retencion[]" onchange="traerPorcentaje(\'' +
      filaImpuesto +
      '\')"><option value="">--Seleccione--</option><option value="RENTA">Renta</option><option value="IVA">Iva</option></select></td>';
    html +=
      '<td><select class="form-control select2_porcentaje" style="width: 100%;" id="porcentaje_retencion' +
      filaImpuesto +
      '" name="porcentaje_retencion[]" onchange="asignarCodigo(\'' +
      filaImpuesto +
      '\')"><option value="">--Seleccione--</option></select></td>';
    html +=
      '<td><input type="text" class="form-control text-right" id="codigo_retencion' +
      filaImpuesto +
      '" name="codigo_retencion[]" placeholder="-" readonly /></td>';
    html +=
      '<td><input type="text" class="form-control text-right" id="base_retencion' +
      filaImpuesto +
      '" name="base_retencion[]" placeholder="0.00" readonly /></td>';
    html +=
      '<td><input type="text" class="form-control text-right" id="valor_retencion' +
      filaImpuesto +
      '" name="valor_retencion[]" placeholder="0.00" /></td>';
    html +=
      '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarImpuesto(\'' +
      filaImpuesto +
      '\');"><i class="fa fa-trash-alt"></i></a></td>';
    html += "</tr>";
    $("#impuestos").append(html);
  } else {
    mensajes("info", "Debe seleccionar una factura");
  }
}
function agregarDetCompra() {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/obtenerDetalleCompra",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: $("#id_factura").val(),
    },
    datatype: "html",
    success: function (html) {
      //console.log(html);
      $("#detalle_compra").html(html);
    },
    error: function (json) {
      console.log(json);
    },
  });
}
function total_detalle_compra() {
  var tabla = document.getElementById("tabla_compra");
  var checkbox = document.getElementsByName('ch[]');
  var subtotal0 = document.getElementsByName('subtotal0[]');
  console.log(subtotal0, checkbox);
  var acum0 = 0;
  console.log(tabla.rows.length);
  for (var i = 0; i < tabla.rows.length - 3; i++) {
    // Obtener el checkbox de la fila actual

    console.log(checkbox[i]);
    // Verificar si el checkbox está marcado
    if (checkbox[i].checked) {
      console.log("Checkbox en la fila " + (i + 1) + " está marcado.");
      acum0 = acum0 + subtotal0[i].value;

    } else {
      console.log("Checkbox en la fila " + (i + 1) + " no está marcado.");
    }
    console.log(acum0);
  }
}
function eliminarImpuesto(id) {
  $("#tr_" + id).remove();
  filaImpuesto--;
}
function traerPorcentaje(fil) {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/obtenerPorcentajeRetenciones",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      tipo: $("#tipo_retencion" + fil).val(),
    },
    datatype: "json",
    success: function (json) {
      if ($("#tipo_retencion" + fil).val() != "") {
        $("#porcentaje_retencion" + fil).empty();
        $("#porcentaje_retencion" + fil).append(
          '<option value="">--Seleccione--</option>'
        );
        $.each(json, function (key, registro) {
          $("#porcentaje_retencion" + fil).append(
            '<option value=" ' +
            registro.id +
            "|" +
            registro.codigo +
            "|" +
            registro.porcentaje +
            '">' +
            registro.codigo +
            " | " +
            registro.descripcion +
            " | " +
            registro.porcentaje +
            "</option>"
          );
        });
        if ($("#tipo_retencion" + fil).val() == "RENTA") {
          $("#base_retencion" + fil).val($("#subtotal").val());
        } else {
          $("#base_retencion" + fil).val($("#iva").val());
        }
        $("#porcentaje_retencion" + fil).select2();
      } else {
        $("#porcentaje_retencion" + fil).empty();
        $("#porcentaje_retencion" + fil).append(
          '<option value="">--Seleccione--</option>'
        );
      }
    },
    error: function (json) {
      console.log(json);
    },
  });
}
function asignarCodigo(fil) {
  var d = $("#porcentaje_retencion" + fil)
    .val()
    .split("|");
  var valorRetencion =
    (parseFloat(d[2]) * parseFloat($("#base_retencion" + fil).val())) / 100;
  $("#codigo_retencion" + fil).val(d[1]);
  $("#valor_retencion" + fil).val(valorRetencion);
  calcularRetencion();
}
function calcularRetencion() {
  var renta = 0;
  var iva = 0;
  $("#frm_")
    .find(":input")
    .each(function () {
      var elemento = this;
      if (/tipo_retencion/.test(elemento.id)) {
        var to = elemento.id.replace("tipo_retencion", "");
        var d = $("#" + elemento.id).val();
        if (d == "RENTA") {
          renta += parseFloat($("#valor_retencion" + to).val());
        } else if (d == "IVA") {
          iva += parseFloat($("#valor_retencion" + to).val());
        }
      }
    });
  $("#renta").val(renta);
  $("#iva").val(iva);
  $("#total").val(parseFloat(parseFloat(renta) + parseFloat(iva)).toFixed(2));
}

function validarForm() {
  let formulario = document.forms["frm_comprobante"];

  let array = [];

  if (formulario.nombre_acreedores.value == "") {
    array.push("nombre acreedor");
  } else if (formulario.fecha.value == "") {
    array.push("fecha");
  } else if (formulario.forma_pago.value == "") {
    array.push("forma pago");
  } else if (
    formulario.forma_pago.value == 2 ||
    formulario.forma_pago.value == 3
  ) {
    if (formulario.forma_pago.value == 2) {
      if (formulario.banco.value == "") {
        array.push("banco");
      } else if (formulario.divisas.value == "") {
        array.push("divisas");
      } else if (formulario.fecha_cheque.value == "") {
        array.push("fecha cheque");
      } else if (formulario.num_cheque.value == "") {
        array.push("numero cheque");
      } else if (formulario.valor.value == "") {
        array.push("valor");
      } else if (formulario.sucursal.value == "") {
        array.push("sucursal");
      } else if (formulario.concepto.value == "") {
        array.push("concepto");
      }
    }

    if (formulario.forma_pago.value == 3) {
      if (formulario.banco.value == "") {
        array.push("banco");
      } else if (formulario.divisas.value == "") {
        array.push("divisas");
      } else if (formulario.valor.value == "") {
        array.push("valor");
      } else if (formulario.sucursal.value == "") {
        array.push("sucursal");
      } else if (formulario.concepto.value == "") {
        array.push("concepto");
      }
    }
  } else if (formulario.divisas.value == "") {
    array.push("divisas");
  } else if (formulario.valor.value == "") {
    array.push("valor");
  } else if (formulario.sucursal.value == "") {
    array.push("sucursal");
  } else if (formulario.concepto.value == "") {
    array.push("concepto");
  }
  return array;
}

function guardar() {
  document.getElementById("btnguardarComprobante").disabled = true;
  let final_valor_cheque = parseFloat($("#valor").val());
  let total_suma = parseFloat($("#total_suma").val());
  let superavit = parseInt($("#verificar_superavit").val());
  let valForm = validarForm();
  if (valForm.length == 0) {
    if (superavit == 1) {
      if (total_suma < final_valor_cheque) {
        let resta_superavit = parseFloat(final_valor_cheque - total_suma);
        let resta_fixed = resta_superavit.toFixed(2, 2);
        $("#total_favor").val(resta_superavit);
        if (
          confirm(
            `Existe un superávit de ${resta_fixed} en la cobertura de las deudas.¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?`
          )
        ) {
          $("#total_favor").val(resta_superavit);
          $("#superavit").val(0);
          $("#comprobarx").val(1);
          mensajes(
            "question",
            $("#pregunta").val(),
            "frm_comprobante",
            "v3/proveedores/comprobantedeegreso"
          );
          document.getElementById("btnguardarComprobante").disabled = false;
        } else {
          mensajes(
            "question",
            $("#pregunta").val(),
            "frm_comprobante",
            "action",
            "v3/proveedores/comprobantedeegreso"
          );
          document.getElementById("btnguardarComprobante").disabled = false;
        }
      } else {
        if (
          confirm(
            `Existe un superávit de ${final_valor_cheque} en la cobertura de las deudas. \n ¿Desea que éste valor sea considerado como un Débito a favor de la Empresa?`
          )
        ) {
          $("#superavit").val(1);
          $("#total_favor").val(final_valor_cheque);
          mensajes(
            "question",
            $("#pregunta").val(),
            "frm_comprobante",
            "action",
            "v3/proveedores/comprobantedeegreso"
          );
          document.getElementById("btnguardarComprobante").disabled = false;
        }
      }
    } else {
      mensajes(
        "question",
        $("#pregunta").val(),
        "frm_comprobante",
        "action",
        "v3/proveedores/comprobantedeegreso"
      );
      document.getElementById("btnguardarComprobante").disabled = false;
    }
  } else {
    mensajes("warning", "Ingrese información del campo " + valForm.join(","));
    document.getElementById("btnguardarComprobante").disabled = false;
  }
}

function changeStyle() {
  let estilo = document.getElementById("valor");
  estilo.style.textAlign = "right";
}

function anterioresAnticipos() {
  let id_proveedor = document.getElementById("nombre_acreedores").value;
  $.ajax({
    type: `get`,
    url: "v3/" + $("#controlador").val() + "/buscarAntAnticipos",
    data: {
      id_proveedor: id_proveedor,
    },
    datatype: "json",
    success: function (data) {
      $("#mostrar_anticipos").empty();
      $("#mostrar_anticipos").append(data.html);
    },
    error: function (data) { },
  });
}

function verComprobante(id_Guia) {
  window.open(
    $("#base_url").val() +
    "/v3/proveedores/crearComprobanteEgreso?id=" +
    id_Guia,
    "_self"
  );
}

function verCruce(id_Cruce) {
  window.open(
    $("#base_url").val() +
    "/v3/proveedores/crearCruceValor?id=" +
    id_Cruce,
    "_self"
  );
}

function regresar() {
  history.back();
}

function mostrarPDF(id) {
  $.ajax({
    type: `get`,
    url: "v3/" + $("#controlador").val() + "/mostrarPdf",
    data: {
      id: id,
    },
    datatype: "json",
    success: function (data) {
      $("#contenidoHtml").html(data);
      $("#modalTotal").modal("show");
    },
    error: function (data) { },
  });
}

function buscarCruce() {

  if ($("#desde").val() == "") {

  } else if ($("#hasta").val() == "") {
    return mensajes("warning", "Debe ingresar la fecha de final...");
  } else {
    $("#tableCruceValor").dataTable().fnClearTable();
    $("#tableCruceValor").dataTable().fnDestroy();
    $("#tableCruceValor").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
        url: "v3/" + $("#controlador").val() + "/" + "allCruce",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          d: $("#desde").val(),
          h: $("#hasta").val(),
        },
        error(error) {
          console.log(error);
        },
      },
      columns: [
        {
          className: "text-center",
          data: "secuencia",
        },
        {
          className: "text-left",
          data: "fecha_pago",
        },
        {
          className: "text-center",
          data: "id_asiento_cabecera",
        },
        {
          className: "text-center",
          data: "nombrecomercial",
        },
        {
          className: "text-left",
          data: "detalle",
        },
        {
          className: "text-right",
          data: "total_disponible",
        },
        {
          data: "nombre_crea",
        },
        {
          data: "nombre_modi",
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
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> Inactivar';
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCruce(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              (row.estado != 0
                ? '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="preguntarCambioEstadoCruce(\'' +
                row.id +
                "','" +
                row.estado +
                "','CruceValor');\">" +
                accion
                : "") +
              "</a><a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFCruce('" +
              row.id +
              "');\"><i class='fas fa-file-pdf' style='color:gray;'></i> PDF</a></div></div>";
            return botones;
          },
        },
      ],
    });
  }
}


function buscarComprobante() {
  if ($("#desde").val() == "") {

  } else if ($("#hasta").val() == "") {
    return mensajes("warning", "Debe ingresar la fecha de final...");
  } else {
    $("#tableComprobanteEgreso").dataTable().fnClearTable();
    $("#tableComprobanteEgreso").dataTable().fnDestroy();
    $("#tableComprobanteEgreso").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info:
          $("#mostrando").val() +
          " _START_ " +
          $("#a").val() +
          " _END_ " +
          $("#de").val() +
          " _TOTAL_ " +
          $("#entradas").val(),
        infoEmpty:
          $("#mostrando").val() +
          " 0 " +
          $("#a").val() +
          " 0 " +
          $("#de").val() +
          " 0 " +
          $("#entradas").val(),
        infoFiltered:
          "(" +
          $("#filtrado").val() +
          " " +
          $("#de").val() +
          " _MAX_ total " +
          $("#entradas").val() +
          ")",
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
        url: "v3/" + $("#controlador").val() + "/" + "allComprobantes",
        type: "post",
        data: {
          d: $("#desde").val(),
          h: $("#hasta").val(),
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
          data: "id_asiento_cabecera",
        },
        {
          className: "text-center",
          data: "secuencia",
        },
        {
          className: "text-center",
          data: "fecha_cheque",
        },
        {
          className: "text-center",
          data: "no_cheque",
        },
        {
          data: "razonsocial",
        },
        {
          data: "descripcion",
        },
        {
          className: "text-right",
          data: "valor_pago",
          render: function (data, type, row) {
            return parseFloat(row.valor_pago).toFixed(2);
          },
        },
        {
          className: "text-center",
          data: "nombre_crea",
        },
        {
          className: "text-center",
          data: "nombre_modi",
          render: function (data, type, row) {
            let estado = "";
            if (row.estado == 0) {
              estado = row.nombre_modi;
            }
            return estado;
          },
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado =
              row.estado == 1
                ? '<label class="text-success">' + $('#activo').val() + '</label>'
                : '<label class="text-danger">' + $('#activo').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-check-circle" style="color:green;"></i> ' +
              $("#activar").val();
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-times-circle" style="color:red;"></i> ' +
                $("#inactivo").val();
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verComprobante(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              (row.estado != 0
                ? '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="preguntarCambioEstado(\'' +
                row.id +
                "','" +
                row.estado +
                "','ComprobanteEgreso');\">" +
                accion
                : "") +
              "</a><a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFCruce('" +
              row.id +
              "');\"><i class='fas fa-file-pdf' style='color:gray;'></i> PDF</a></div></div>";
            return botones;
          },
        },
      ],
    });
  }
}

function preguntarCambioEstadoCruce(id) {
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
      testCruce(id);
    }
  });
}

async function testCruce(id) {
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
        url: "v3/" + $('#controlador').val() + "/estadoCruceValor",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          text: text,
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

function preguntarCambioEstado(id, estado, tipo) {
  Swal.fire({
    title: "¿Desea Anular este comprobante?",
    text: $("#informa").val(),
    icon: "info",
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      cambiarEstado(id, estado, tipo);
    }
  });
}

function addNewRow3(pos, fecha, tipo, fact_numero, observacion, valor, id) {
  var markup = "";
  var num = parseInt(pos) + 1;
  markup =
    "<tr>" +
    "<td> <input type='text' class='form-control input-sm' name='emision" +
    pos +
    "' id='emision" +
    pos +
    "' readonly='' value='" +
    fecha +
    "'> <input type='hidden' name='id_act" +
    pos +
    "' value='" +
    id +
    "'>  </td>" +
    "<td> <input type='text' class='form-control input-sm' name='tipo" +
    pos +
    "' id='tipo" +
    pos +
    "' value='" +
    tipo +
    "' readonly=''> </td>" +
    "<td> <input type='text' class='form-control input-sm' name='numero_a" +
    pos +
    "' id='numero_a" +
    pos +
    "' value='" +
    fact_numero +
    "' readonly=''> </td>" +
    "<td> <input type='text' class='form-control input-sm'  name='concepto" +
    pos +
    "' id='concepto" +
    pos +
    "' value='" +
    observacion +
    "' readonly=''> </td>" +
    "<td> <input type='text' style='background-color: #c9ffe5; ' class='form-control input-sm' name='div" +
    pos +
    "' id='div" +
    pos +
    "' value='$' readonly=''> </td>" +
    "<td> <input type='text' style='background-color: #c9ffe5; ' class='form-control input-sm' name='saldo_a" +
    pos +
    "' value='" +
    valor +
    "' id='saldo_a" +
    pos +
    "' readonly=''> </td>" +
    "<td> <input type='text' style='background-color: #c9ffe5; text-align: center;' class='form-control input-sm' name='abono_a" +
    pos +
    "' id='abono_a" +
    pos +
    "' onchange='validar_saldos(" +
    pos +
    ")'></td>" +
    "<td> <input type='text'  class='form-control input-sm' name='nuevo_saldo" +
    pos +
    "' value='0.00'  id='nuevo_saldo_a" +
    pos +
    "' readonly=''><input type='hidden' name='visibilidad" +
    pos +
    "' id='visibilidad" +
    pos +
    "' value='0'></td>" +
    "</tr>";
  return markup;
}

function buscador_anticipos() {
  let proveedor = $("#proveedor").val();
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/obtener_anticipos",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      proveedor: proveedor,
    },
    success: function (data) {
      if (data.value != "no") {
        $("#mostrar_detalles_valor").empty();
        var fila = 0;
        for (i = 0; i < data.length; i++) {
          var row = addNewRow3(
            i,
            data[i].fecha_asiento,
            "BAN-ND",
            data[i].secuencia,
            data[i].observacion,
            data[i].valor_abono,
            data[i].id
          );
          if (data[i].id_referencia == null) {
            row = addNewRow3(
              i,
              data[i].fecha_asiento,
              "ACR-EG",
              data[i].secuencia,
              data[i].observacion,
              data[i].valor_abono,
              data[i].id
            );
          }

          $("#tablaValores").append(row);
          fila = i;
        }
        $("#contador_total").val(fila);
      }
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function addNewRowPro(
  pos,
  fecha,
  valor,
  factura,
  fact_numero,
  observacion,
  valor_nuevo,
  id
) {
  var markup = "";
  var num = parseInt(pos) + 1;
  markup =
    "<tr>" +
    "<td> <input class='form-control' type='text' name='vence" +
    pos +
    "' id='vence" +
    pos +
    "' readonly='' value='" +
    fecha +
    "'> </td>" +
    " <input class='form-control' type='hidden'   name='id_actualiza" +
    pos +
    "' id='id_actualiza" +
    pos +
    "'  value='" +
    id +
    "'>" +
    "<td> <input class='form-control' type='text' name='tipo" +
    pos +
    "' id='tipo" +
    pos +
    "' value='" +
    factura +
    "' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' name='numero" +
    pos +
    "' id='numero" +
    pos +
    "' value='" +
    fact_numero +
    "' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' name='concepto_a" +
    pos +
    "' id='concepto_a" +
    pos +
    "' value='Fact # " +
    fact_numero +
    " Prov: " +
    observacion +
    "' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' style='background-color: #c9ffe5;' name='div" +
    pos +
    "' id='div" +
    pos +
    "' value='$' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; ' name='saldo" +
    pos +
    "' value='" +
    valor +
    "' id='saldo" +
    pos +
    "' readonly=''> </td>" +
    "<td> <input class='form-control' type='text' style='background-color: #c9ffe5; text-align: center;' name='abono" +
    pos +
    "' id='abono" +
    pos +
    "' onchange='validar_td(" +
    pos +
    ")'></td>" +
    "<td> <input class='form-control' type='text' style=' text-align: left;' name='nuevo_saldo" +
    pos +
    "' value='" +
    valor +
    "' id='nuevo_saldo" +
    pos +
    "' readonly=''></td>" +
    "</tr>";
  return markup;
}

function buscar_proveedor_cruce() {
  let id_proveedor = $("#proveedor").val();
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/buscarProveedor",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      id_proveedor: id_proveedor,
    },
    success: function (data) {
      if (data.value != "no resultados") {
        $("#detalleDeudasProveedor").empty();
        var fila = 0;
        for (i = 0; i < data[5].length; i++) {
          if (data[5][i].tipo == 1) {
            var row = addNewRowPro(
              i,
              data[5][i].fecha_asiento,
              data[5][i].valor_contable,
              "COM-FA",
              data[5][i].numero,
              data[5][i].proveedor + "" + data[5][i].observacion,
              data[5][i].valor_nuevo,
              data[5][i].id
            );
            $("#")
            $("#tableDeudasProveedor").append(row);
            fila = i;
          } else {
            var row = addNewRowPro(
              i,
              data[5][i].fecha_asiento,
              data[5][i].valor_contable,
              "COM-FACT",
              data[5][i].numero,
              data[5][i].proveedor + " " + data[5][i].observacion,
              data[5][i].valor_nuevo,
              data[5][i].id
            );
            $("#tableDeudasProveedor").append(row);
            fila = i;
          }
        }
        $("#contador").val(fila);
        let sumaDeudas = 0;
        for (let index = 0; index <= $("#contador").val(); index++) {
          sumaDeudas += Number($("#saldo" + index).val());
        }
        $("#total_deudas").val(sumaDeudas);
      }
    },
    error: function (data) {
      mensajes("error", "Existión un error en el proceso");
    },
  });
}

function cambiar_nombre_proveedor() {
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/compra_buscar_proveedornombre",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      id: $("#proveedor").val(),
    },
    success: function (data) {
      if (data.value != "no") {
        buscar_proveedor_cruce();
        buscador_anticipos();
      }
    },
    error: function (data) {
      console.log(data);
    },
  });
}

function suma_totales2() {
  contador = 0;
  iva = 0;
  total = 0;
  sub = 0;
  descu1 = 0;
  total_fin = 0;
  descu = 0;
  cantidad = 0;

  $("#mostrar_detalles_valor tr").each(function () {
    $(this).find("td")[0];
    cantidad = parseFloat($("#abono_a" + contador).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
    contador = contador + 1;
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#total_suma_a").val(total.toFixed(2, 2));

  //alert(total_fin);
}

function suma_totales3() {
  contador = 0;
  iva = 0;
  total = 0;
  sub = 0;
  descu1 = 0;
  total_fin = 0;
  descu = 0;
  cantidad = 0;

  $("#mostrar_detalles_valor tr").each(function () {
    $(this).find("td")[0];
    cantidad = parseFloat($("#saldo_a" + contador).val());
    if (!isNaN(cantidad)) {
      total += cantidad;
    }
    contador = contador + 1;
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#saldoax").val(total.toFixed(2, 2));
}

function callRec(abono) {
  for (let index = 0; index <= $("#contador").val(); index++) {
    $("#abono" + index).val(abono);
    validar_td(index);
  }
}

function SumarTodAbono() {
  let sum = 0;
  for (let index = 0; index <= $("#contador_total").val(); index++) {
    sum += Number($("#abono_a" + index).val());
    $("#total_abono").val(sum.toFixed(2, 2));
  }
}

function validar_saldos(id) {
  if (id != null) {

    var valor = parseFloat($("#saldo_a" + id).val());
    var abono = parseFloat($("#abono_a" + id).val());
    SumarTodAbono();
    suma_totales2();
    suma_totales3();
    var sumax = parseFloat($("#saldoax").val());
    var total = parseFloat($("#valores").val());
    if (isNaN(total)) {
      total = 0;
    }
    var cantidad = parseFloat($("#total_suma_a").val());
    if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
      if (abono > 0) {
        if (cantidad <= valor && cantidad <= sumax) {
          $("#valores").val(cantidad.toFixed(2, 2));
          $("#abono_a" + id).val(abono.toFixed(2, 2));
          var totalx = valor - abono;
          $("#nuevos_saldo").val(totalx.toFixed(2, 2));
          $("#nuevo_saldo_a" + id).val(totalx.toFixed(2, 2));
          callRec(abono);

        } else {
          $("#abono_a" + id).val(abono.toFixed(2, 2));
          suma_totales2();
          var cantidad = parseFloat($("#total_suma_a").val());
          $("#valores").val(cantidad.toFixed(2, 2));
          callRec(abono);
        }
      } else {
        suma_totales2();
        $("#abono_a" + id).val(abono.toFixed(2, 2));
        $("#valores").val(cantidad.toFixed(2, 2));
      }
    } else {
      abono = 0;
      valor = 0;
      $("abono_a" + id).val(valor.toFixed(2, 2));
    }
  }
}

function suma_totales() {
  contador = 0;
  iva = 0;
  total = 0;
  sub = 0;
  descu1 = 0;
  total_fin = 0;
  descu = 0;
  cantidad = 0;
  $("#detalleDeudasProveedor tr").each(function () {
    cantidad = parseFloat($("#abono" + contador).val());
    if (!isNaN(cantidad)) {
      total += cantidad;

      if (Number($("#saldo" + contador).val()) >= Number($("#abono" + contador).val())) {
        let tot = Number($("#saldo" + contador).val()) - Number($("#abono" + contador).val());
        $("#nuevo_saldo" + contador).val(tot.toFixed(2, 2));
      } else {
        $("#nuevo_saldo" + contador).val("0.00");
      }
    }
    contador = contador + 1;
  });

  if (isNaN(total)) {
    total = 0;
  }

  $("#total_suma").val(total.toFixed(2, 2));
}

function validar_td(id) {
  var valor = parseFloat($("#valores").val());
  var abono = parseFloat($("#abono" + id).val());
  suma_totales();
  var cantidad = parseFloat($("#total_suma").val());
  if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
    if (cantidad <= valor) {
      $("#abono" + id).val(abono.toFixed(2, 2));
    } else {
      valor = 0;
      $("#abono" + id).val(valor.toFixed(2, 2));
      $("#nuevo_saldo" + id).val($("#saldo" + id).val());
      mensajes("error", "No se puede superar el valor del cheque");
    }
  } else {
    abono = 0;
    valor = 0;
    $("abono" + id).val(valor.toFixed(2, 2));
  }
}


$("#guardarCruceValores").on("click", function () {

  let tabla = document.querySelector("#tablaValores tbody");
  let cant = tabla.querySelectorAll("tr").length
  let tablaUno = document.querySelector("#tableDeudasProveedor tbody");
  let cantUno = tablaUno.querySelectorAll("tr").length
  let vali1 = cant - 1;
  let vali2 = cantUno - 1;
  if ($("#proveedor").val() == '') {
    return mensajes("warning", "Debe ingresar el proveedor...");
  } else if ($("#concepto").val() == '') {
    return mensajes("warning", "Debe ingresar el concepto...");
  } else if ($("#sucursalc").val() == '') {
    return mensajes("warning", "Debe ingresar la sucursal...");
  } else if ($("#abono_a" + vali1).val() == '') {
    for (let index = 0; index < cant; index++) {
      if ($("#abono_a" + index).val() == '') {
        return mensajes("warning", `Debe ingresar el ebono en la final ${index + 1} en la tabla de valores a favor...`);
      }
    }
  } else if ($("#abono" + vali2).val() == '') {
    for (let index = 0; index < cantUno; index++) {
      if ($("#abono" + index).val() == '') {
        return mensajes("warning", `Debe ingresar el ebono en la final ${index + 1} en la tabla de deudas del proveedor...`);
      }
    }
  } else {
    mensajes("question", $("#pregunta").val(), "frm_cruceValores", "action", "v3/proveedores/crucedevalores");
  }
});

function mostrarPDFCruce(id) {
  window.open("v3/" + $("#controlador").val() + "/mostrarPddCruce?id=" + id, "_blank");

}


