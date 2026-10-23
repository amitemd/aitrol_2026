var fila = 0;
var filaCrucep = 0;
var filaImpuesto = 0;
var filaRubros = 0;
var btnRuc = $(".ladda-button-demo-ruc").ladda();
var btnCed = $(".ladda-button-demo-ced").ladda();
var btnCer = $(".ladda-button-demo-cer").ladda();
var btnProvLogo = $(".ladda-button-demo-provlogo").ladda();
$(document).ready(function () {

  $("#sucursal").on("change", function () {
    let sucursal = $(this).val();
    $.ajax({
      type: "post",
      url: "v3/" + $("#controlador").val() + "/getPuntoEmision",
      headers: {
        "X-CSRF-TOKEN": $("input[name=_token]").val(),
      },
      datatype: "json",
      data: {
        id_sucursal: sucursal,
      },
      success: function (data) {
        if (sucursal != 0) {
          $("#punto_emision").empty();
          $.each(data, function (key, registro) {
            var select = "";
            if (registro.codigo_caja == "100") select = "selected";
            $("#punto_emision").append(
              "<option value=" +
              registro.id +
              " " +
              select +
              ">" +
              registro.codigo_sucursal +
              "-" +
              registro.codigo_caja +
              "</option>"
            );
          });
        } else {
          $("#punto_emision").empty();
        }
      },
      error: function (data) { },
    });
  });

  $('input[rel="txtTooltip"]').tooltip();
  $("#tableComprobanteEgresoVarios").DataTable({
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
      url: "v3/" + $("#controlador").val() + "/" + "allComprobantesVarios",
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
        render: function (data, type, row) {
          return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
        }
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
        data: "nro_cheque",
      },
      {
        data: "id_proveedor",
      },
      {
        data: "descripcion",
      },
      {
        className: "text-right",
        data: "valor",

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
            '<i class="fas fa-check-circle" style="color:red;"></i> Anulado';
          if (row.estado == 1) {
            accion =
              '<i class="fa fa-trash" style="color:red;"></i> Anular';
          }
          var botones =
            '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
            $("#colorBoton").val() +
            ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
            $("#colorBoton").val() +
            ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verComprobantevarios(\'' +
            row.id +
            '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
            $("#ver").val() + "<a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFingreso('" + row.id + "');\"><i class='fas fa-file-pdf' style='color:gray;'></i> PDF</a>" +
            (row.estado != 0
              ? '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoEgresoVario(\'' +
              row.id +
              "','" +
              row.estado +
              "','ComprobanteEgreso');\">" +
              accion
              : "") +
            "</a></div></div>";
          return botones;
        },
      },
    ],
  });
  $("#btnguardarComprobanteEgresoVarios").click(function () {
    if ($("#sucursal").val() === "") {
      return mensajes("info", "Debe elegir una sucursal");
    } else if ($("#valor").val() === "") {
      return mensajes("info", "Debe ingresar el valor");
    } else if ($("#concepto").val() === "") {
      return mensajes("info", "Debe ingresar concepto");
    } else if ($("#beneficiario").val() === "") {
      return mensajes("info", "Debe ingresar beneficiario");
    } else if ($("#banco").val() === "") {
      return mensajes("info", "Debe elegir un banco");
    } else if ($("#forma_pago").val() === "") {
      return mensajes("info", "Debe elegir una forma de pago");
    }
    var cuentasValidas = true;
    $(".select2_cuenta").each(function () {
      if ($(this).val() === "") {
        cuentasValidas = false;
        return false;
      }
    });
    if (!cuentasValidas) {
      return mensajes("info", "Debe elegir una cuenta");
    } else if ($("#nota").val() === "") {
      return mensajes("info", "Debe ingresar una Nota");
    } else {
      mensajes("question", $("#pregunta").val());
    }
    var debe = parseFloat($('#debe_final').val());
    var haber = parseFloat($('#haber_final').val());
    if (debe === haber) {
      mensajes("question", $("#pregunta").val(), "frm_comprobanteegresovarios", "action", "v3/ventas/facturar/facturaconglomerada");
    }
  });
  try {
    document.getElementById('valor').addEventListener('input', function () {
      mostrarValorEnHaber();
    });
  } catch (error) {
    console.log(error);
  }
  function mostrarValorEnHaber() {
    valor = document.getElementById('valor')?.value;

    if (valor) {
      document.getElementById('haber_final').value = valor;
    }
  }
  $(".select2_ajax").select2({
    minimumInputLength: 3,
    cache: true,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getTraerFacturas",
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
              text: item.serie + " - " + item.numero + " | " + item.observacion,
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
  $(".factura").select2({
    minimumInputLength: 3,
    cache: true,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/obtenerFactura",
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
              text: item.serie + " - " + item.numero + " | " + item.observacion + " | " + item.razonsocial,
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
  let id_proveedor = $("#editar_comprobante").val();
  if (id_proveedor !== "") {
    $("#forma_pago").val($("#editar_forma_pago").val()).trigger("change");
    $("#sucursal").val($("#editar_sucursal").val()).trigger("change");
    $("#divisas").val(1).trigger("change");
    $("#banco").val($("#editar_banco").val()).trigger("change");
    $("#num_cheque").val($("#editar_numerocheque").val());
    $("#fecha_cheque").val($("#editar_fechacheque").val());
    $("#valor").val($("#editar_valor").val());
    $("#nombre_acreedores").select2({ disabled: true, });
    $("#girado").prop("readonly", true);
    $("#fecha").prop("readonly", true);
    $("#banco").select2({ disabled: true, });
    $("#divisas").select2({ disabled: true, });
    $("#fecha_cheque").prop("readonly", true);
    $("#num_cheque").prop("readonly", true);
    $("#valor").prop("readonly", true);
    $("#forma_pago").select2({ disabled: true, });
    $("#sucursal").select2({ disabled: true, });
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
  btnProvLogo.click(function () {
    btnProvLogo.ladda("start");
  });
  try {
    $("#tableProveedores").DataTable({
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
            var logo = '<i class="fas fa-user-tie fa-3x" style="color:#91B2F3;"></i>';
            if (row.logo !== null) {
              logo = '<img src="../storage/app/logo/' + row.logo + '" style="width: 70px;cursor:pointer;" onclick="mostrarImg(\'../storage/app/logo/' + row.logo + "')\"/>";
            }
            return logo;
          },
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">' + $("#activo").val() + "</label>" : '<label class="text-danger">' + $("#inactivo").val() + "</label>";
            return estado;
          },
        },
        {
          className: "text-center",
          data: "created_at",
          render: function (data, type, row) {
            return getfecha(row.created_at, null, "-");
          },
        },
        {
          className: "text-center",
          data: "usuariocrea",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProveedor(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Proveedor');\">" + accion + "</a></div></div>";
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
      order: [[0, "desc"]],
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
            var estado =
              '<label class="text-success">' + $("#activo").val() + "</label>";
            if (row.estado == 0) {
              estado =
                '<label class="text-danger">' +
                $("#inactivo").val() +
                "</label>";
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
            var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> Anular';
            }
            var botones = '<div class="btn-group">' +
              '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown">' +
              '<i class="fas fa-list-ol"></i>' +
              '</button>' +
              '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown">' +
              '<span class="sr-only">Toggle Dropdown</span>' +
              '</button>' +
              '<div class="dropdown-menu" role="menu">' +
              '<a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' + row.id + '\', \'' + row.nro_comprobante + '\');">' +
              '<i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() +
              ' </a>' +
              '<div class="dropdown-divider"></div>' +
              '<a class="dropdown-item" href="javascript:;" ' + (row.estado == 1 ? 'onclick="cambiarEstadoRetencionesP(\'' + row.id + '\', \'' + row.estado + '\', \'Retenciones\')"' : '') + '>' +
              accion +
              '</a>' +
              '</div>' +
              '</div>';


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
            var estado =
              '<label class="text-success">' + $("#activo").val() + "</label>";
            if (row.estado == 0) {
              estado =
                '<label class="text-danger">' +
                $("#inactivo").val() +
                "</label>";
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
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProveedor(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' +
              row.id +
              "','" +
              row.estado +
              "','SaldosIniciales');\">" +
              accion +
              "</a></div></div>";
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
      lengthChange: true,
      autoWidth: true,
      iDisplayLength: 50,
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
      pageLength: 50,
      autoWidth: true,
      order: [[0, "desc"]],
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
          className: "text-center",
          data: "fecha",
        },
        {
          className: "text-center",
          data: "nro_comprobante",
        },
        {
          className: "text-center",
          data: "id_asiento_cabecera",
          render: function (data, type, row) {
            return (
              '<a href="javascript:;" onclick="visualizarAsiento(\'' +
              row.id_asiento_cabecera +
              "');\">" +
              row.id_asiento_cabecera +
              "</a>"
            );
          },
        },
        {
          data: "concepto",
        },
        {
          data: "nombrecomercial",
        },
        {
          className: "text-right",
          data: "valor_contable",
        },
        {
          className: "text-center",
          data: "usuariocrea",
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
              '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verNotadeCredito(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              '</a><div class="dropdown-divider"></div>';
            if (row.estado == 1) {
              botones +=
                '<a class="dropdown-item" href="javascript:;" onclick="anularNotaCredito(\'' +
                row.id +
                "');\">" +
                accion +
                "</a>";
            } else {
              botones +=
                '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' +
                accion +
                "</a>";
            }
            botones += "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableNotadedebito").DataTable({
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
      pageLength: 50,
      autoWidth: true,
      order: [[0, "desc"]],
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
          className: "text-center",
          data: "fechand",
        },
        {
          className: "text-center",
          data: "nro_comprobante",
        },
        {
          className: "text-center",
          data: "id_asiento_cabecera",
          render: function (data, type, row) {
            return (
              '<a href="javascript:;" onclick="visualizarAsiento(\'' +
              row.id_asiento_cabecera +
              "');\">" +
              row.id_asiento_cabecera +
              "</a>"
            );
          },
        },
        {
          data: "concepto",
        },
        {
          data: "nombrecomercial",
        },
        {
          className: "text-right",
          data: "valor_contable",
        },
        {
          className: "text-center",
          data: "usuariocrea",
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
              '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verNotadeDebito(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              '</a><div class="dropdown-divider"></div>';
            if (row.estado == 1) {
              botones +=
                '<a class="dropdown-item" href="javascript:;" onclick="anularNotaDebito(\'' +
                row.id +
                "');\">" +
                accion +
                "</a>";
            } else {
              botones +=
                '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' +
                accion +
                "</a>";
            }
            botones += "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableCruceValor").DataTable({
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
          className: 'text-center',
          data: 'id_asiento_cabecera',
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\');">' + row.id_asiento_cabecera + '</a>';
          }
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
      order: [[0, "desc"]],
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
          className: "text-right",
          data: "secuencia",
        },
        {
          className: "text-center",
          data: "id_asiento_cabecera",
          render: function (data, type, row) {
            return (
              '<a href="javascript:;" onclick="visualizarAsiento(\'' +
              row.id_asiento_cabecera +
              "');\">" +
              row.id_asiento_cabecera +
              "</a>"
            );
          },
        },
        {
          className: "text-center",
          data: "fecha",
        },
        {
          className: "text-right",
          data: "id_proveedor",
        },
        {
          data: "nombrecomercial",
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
          data: "usuariocrea",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado =
              row.estado == 1
                ? '<label class="text-success">' +
                $("#activo").val() +
                "</label>"
                : '<label class="text-danger">' +
                $("#anulado").val() +
                "</label>";
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion =
              '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            if (row.estado == 1) {
              accion =
                '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            var botones =
              '<div class="btn-group hero-accion"><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() +
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verCruceCuentas(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              "</a>";
            botones +=
              "<a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFCruceProv('" +
              row.id +
              "');\"><i class='fas fa-file-pdf' style='color:orange;'></i> PDF</a>";
            botones += "<div class='dropdown-divider'></div>";
            if (row.estado == 1) {
              botones +=
                '<a class="dropdown-item" href="javascript:;" onclick="anularCruceProv(\'' +
                row.id +
                "');\">" +
                accion +
                "</a>";
            } else {
              botones +=
                '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' +
                accion +
                "</a>";
            }
            botones += "</div></div>";
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
      return mensajes(
        "warning",
        "Debe ingresar el ruc del proveedor a crear..."
      );
    }
  });
  $("#informe_saldos").select2({
    allowClear: true,
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--" + $("#seleccione").val() + "--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getProveedorCombo",
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
  $("#proveedorLogo").change(function (e) {
    if (($("#proveedorLogo").val().length = 0)) {
      btnProvLogo.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("proveedorLogo");
      var file = inputFileImage.files[0];
      var fileSize = $("#proveedorLogo")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnProvLogo.ladda("stop");
        $("#proveedorLogo").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("proveedorLogo", file);
        fd.append("tipo_archivo", "proveedorLogo");
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
              btnProvLogo.ladda("stop");
              $("#proveedorLogo").val("");
              $("#logoProveedor").html(imagen[1]);
              $('.tablaDatos').DataTable().ajax.reload();
            } else {
              btnProvLogo.ladda("stop");
              alert(imagen[1]);
              $("#proveedorLogo").val("");
            }
          },
        });
      }
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
              $("#lblRuc").html('Ruc: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=proveedorRuc" target="new">ver/descargar</a>');
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
      return mensajes(
        "warning",
        "Debe ingresar el ruc del proveedor a crear..."
      );
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
              $("#lblCedula").html(
                'Cedula: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=proveedorCedula" target="new">ver/descargar</a>'
              );
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
      error(error) {
        console.log(error);
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
    }else if($("#id_cuentas").val() == ""){
      return mensajes("warning", "Debe seleccionar una cuenta contable" + "...");
    }  else if ($("#id_tipoproveedor").val() == "") {
      return mensajes("warning", "Debe seleccionar un tipo proveedor" + "...");
    }else if ($("#pais").val() == "") {
      return mensajes("warning", "Debe ingresar el país" + "...");
    } else if ($("#direccion").val() == "") {
      return mensajes("warning", $("#alertDireccion").val() + "...");
    }
    else {
      if ($("#tipo").val() == 4 && $("#id").val().length !== 13) {
        return mensajes("warning", $("#alertLongIdentificacion").val() + "...");
      }
      if ($("#tipo").val() == 5 && $("#id").val().length !== 10) {
        return mensajes("warning", $("#alertLongIdentificacion").val() + "...");
      }
      var seleccionado = $("#id_ciudad option:selected").text();
      $("#ciudad").val(seleccionado);
      mensajes("question", $("#pregunta").val(), 'frm_', "action", "", "tableProveedores", "", "myModal");

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
      var nombreInput = document.querySelector("input[name='codigo']");
      if (nombreInput.readOnly) {
        mensajes("question", $("#pregunta").val());
      } else {
        validarCodigo();
      }
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
    let validacion = $("#tr_" + filaImpuesto).find("td").length > 0;
    if ($("#facturas").val() == "") {
      return mensajes("info", "Debe seleccionar una factura");
    } else if ($("#establecimiento").val() == "" || $("#emision").val() == "") {
      return mensajes(
        "info",
        "Seleccione el establecimiento y el punto de emisión de la retención"
      );
    } else if ($("#descripcion").val() == "") {
      return mensajes("warning", "Debe ingresar un concepto para la retención");
    } else if (!validacion) {
      return mensajes("warning", "Debe agregar una fila de impuestos");
    } else if (
      $("#tipo_retencion1" + filaImpuesto).val() === "" ||
      $("#porcentaje_retencion" + filaImpuesto).val() === ""
    ) {
      return mensajes("warning", "Debe agregar el tipo impuesto y procentaje");
    } else {
      mensajes("question", $("#pregunta").val(), "frm_", "action", "v3/proveedores/retenciones");
    }
  });
  $("#btnguardarNotaCredito").click(function () {
    if ($("#fecha_nota").val() == "") {
      return mensajes("info", "Debe ingresar la fecha");
    } else if ($("#fecha_caducidad").val() == "") {
      return mensajes("info", "Debe ingresar la fecha de caducidad");
    } else if ($("#establecimiento").val() == "" || $("#emision").val() == "") {
      return mensajes(
        "info",
        "Ingrese el establecimiento y el punto de emisión de la nota de crédito"
      );
    } else if ($("#secuencia").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el secuencial de la nota de crédito"
      );
    } else if ($("#autorizacion_nota").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el número de autorización de la nota de crédito"
      );
    } else if ($("#fechand").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar la fecha NC de la nota de crédito"
      );
    } else if ($("#tipo_comprobante_nota").val() == "") {
      return mensajes(
        "info",
        "Debe seleccionar el tipo de comprobante de la nota de crédito"
      );
    } else if ($("#credito_tributario").val() == "") {
      return mensajes(
        "info",
        "Debe seleccionar el crédito tributario de la nota de crédito"
      );
    } else if ($("#nota").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar la nota correspondiente a la nota de crédito"
      );
    } else if ($("#descripcion").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el concepto de la nota de crédito"
      );
    } else if ($("#total").val() <= 0) {
      return mensajes(
        "error",
        "Debe seleccionar al menos un detalle para realizar la nota de crédito"
      );
    } else {
      mensajes(
        "question",
        $("#pregunta").val(),
        "frm_",
        "action",
        $("#base_url").val() +
        "/v3/" +
        $("#controlador").val() +
        "/notadecredito"
      );
    }

    /*if ($("#facturas").val() == "") {
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
    }*/
  });
  $("#btnguardarNotaDebito").click(function () {
    if ($("#fecha_nota").val() == "") {
      return mensajes("info", "Debe ingresar la fecha");
    } else if ($("#fecha_caducidad").val() == "") {
      return mensajes("info", "Debe ingresar la fecha de caducidad");
    } else if ($("#establecimiento").val() == "" || $("#emision").val() == "") {
      return mensajes(
        "info",
        "Ingrese el establecimiento y el punto de emisión de la nota de crédito"
      );
    } else if ($("#secuencia").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el secuencial de la nota de crédito"
      );
    } else if ($("#autorizacion_nota").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el número de autorización de la nota de crédito"
      );
    }else if ($("#clave_de_acceso").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el número de autorización de la nota de crédito"
      );
    }
     else if ($("#fechand").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar la fecha NC de la nota de crédito"
      );
    } else if ($("#tipo_comprobante_nota").val() == "") {
      return mensajes(
        "info",
        "Debe seleccionar el tipo de comprobante de la nota de crédito"
      );
    } else if ($("#credito_tributario").val() == "") {
      return mensajes(
        "info",
        "Debe seleccionar el crédito tributario de la nota de crédito"
      );
    } else if ($("#nota").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar la nota correspondiente a la nota de crédito"
      );
    } else if ($("#descripcion").val() == "") {
      return mensajes(
        "info",
        "Debe ingresar el concepto de la nota de crédito"
      );
    } else if ($("#total").val() <= 0) {
      return mensajes(
        "error",
        "Debe seleccionar al menos un detalle para realizar la nota de crédito"
      );
    } else {
      mensajes(
        "question",
        $("#pregunta").val(),
        "frm_",
        "action",
        $("#base_url").val() +
        "/v3/" +
        $("#controlador").val() +
        "/notadedebito"
      );
    }

    /*if ($("#facturas").val() == "") {
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
    }*/
  });
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
        $("#detalleDeudasProveedor #detalleFactura").empty();
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
  $("#secuencia").change(function () {
    var originalString = $("#secuencia").val();
    var paddedString = PadLeft(originalString, 9);
    $("#secuencia").val(paddedString);
  });
  $("#autorizacion_nota").blur(function () {
    if (
      $("#autorizacion_nota").val().length != 10 &&
      $("#autorizacion_nota").val().length != 49
    ) {
      return mensajes(
        "warning",
        "Ingrese al menos 10 dígitos y máximo 49 para la número de autorización"
      );
    }
  });
  $("#serie_factura").on("keydown", function (event) {
    var serie_factura = $("#serie_factura").val();
    var keyCode = event.keyCode;
    if (keyCode != 8) {
      if (serie_factura.length == 3) {
        $("#serie_factura").val(serie_factura + "-");
      }
    }
  });
  $("#autorizacion_factura").blur(function () {
    if (
      $("#autorizacion_factura").val().length != 10 &&
      $("#autorizacion_factura").val().length != 49
    ) {
      return mensajes(
        "warning",
        "Ingrese al menos 10 dígitos y máximo 49 para la número de autorización"
      );
    }
  });
  $("#comprobante_compra").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarFactCompra",
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
  $("#comprobante_compra").change(function () {
    $.ajax({
      url: "v3/" + $("#controlador").val() + "/buscarValor",
      type: "get",
      data: {
        id: $(this).val(),
      },
      dataType: "json",
      success: function (json) {
        if (json != "") {
          $("#info_valor").val(json.valor_contable);
          $("#fecha_factcc").val(json.fecha);
          $("#proveedorcc").val(json.id_proveedor != null ? json.id_proveedor + ' | ' + json.razonsocial : '');
        }
      },
    });
  });
  $("#btnguardarCruceProv").click(function () {
    var regex = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|1\d|2\d|3[01])$/;
    if (!regex.test($("#fechacc").val())) {
      return mensajes("warning", "Ingrese una fecha valida");
    } else if ($("#comprobante_compra").val() == "") {
      return mensajes("warning", "Seleccione la factura para continuar");
    } else if ($("#conceptop").val() == "") {
      return mensajes("warning", "Ingrese el concepto del cruce de cuenta");
    } else if ($("#sucursalcc").val() == "") {
      return mensajes("warning", "Seleccione el establecimiento");
    } else if (filaCrucep <= 0) {
      return mensajes("warning", "Agregue el detalle de cuentas");
    }
    else {
      if (filaCrucep > 0) {
        var validacionCuenta = false;
        var validacionDetalle = false;
        var validacionValores = false;
        $("select[name='cuentap[]']").each((i, v) => {
          if (v.value == "") {
            validacionCuenta = true;
          }
          if ($("#detallep_" + i).val() === "") {
            validacionDetalle = true;
          }
          if ($("#valorp_" + i).val() === "0.00") {
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
      mensajes("question", $("#pregunta").val(), 'frm_', 'action', $('#base_url').val() + '/v3/proveedores/crucedecuentas');
    }
  });
});

function habilitarCampo(pos) {
  let resultado = $("#valorasumir" + pos).prop("readOnly");
  if ($("#retencion" + pos).val() > 0) {
    $("#valorasumir" + pos).prop("readonly", !resultado);
    $("#valorasumir" + pos).val(resultado ? $("#retencion" + pos).val() : "0.00");
  } else {
    $("#retencionIn" + pos).prop('checked', false);
  }
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
    "<td><div style='display: flex;'><input class='form-control' type='text' name='numero" +
    pos +
    "' id='numero" +
    pos +
    "' value='" +
    fact_numero +
    "' readonly=''><i class='fas fa-info-circle' style='color:#2980b9;margin-top:10px;' rel='txtTooltip' title='Fact #:" + fact_numero + " Prov: " + observacion + "' data-toggle='tooltip' data-placement='left' style='color:white;font-size:8px'></i></div></td>" +
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
function sumaTotalesF() {

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

    saldo += parseFloat($("#valor").val());
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
  if (!isNaN(valor, valor_saldo) && !isNaN(valor_saldo)) {
    $("#verificar_superavit").val(1);
  } else {
    $("#verificar_superavit").val(0);
    return mensajes("warning", "Ingrese el valor");
  }
  sumaTotalesF();
}
function buscar_proveedor() {
  console.log("aqui");
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
        console.log(data[5].length);
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
              "ñpol",
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
  $('#logoProveedor').html('<i class="fas fa-user-tie fa-10x" style="color:#91B2F3;"></i>');
  $("#lblRuc").html("Ruc");
  $("#lblCedula").html("Cedula");
  $("#lblVotacion").html("Certificado de votación");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modalProveedor").html('<i class="fa-solid fa-user-tie"></i> ' + $("#nuevo").val() + " Proveedor");
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
      $('#logoProveedor').html('');
      $("#titulo_modalProveedor").html('<i class="fas fa-edit"></i> ' + " Proveedor " + capitalizar(json.nombrecomercial));
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
      $("#provisiones").val(JSON.parse(json.provisiones)).trigger("change");
      if (json.id_plan !== null) {
        $("#id_cuentas").html('<option value="' + json.id_plan + '">' + json.id_cuentas + ' | ' + json.nombre_plan + '</option>').trigger("change");
      } else {
        $("#id_cuentas").html('<option value="">--Seleccionar--</option>').trigger("change");
      }
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
      if (json.logo !== null && json.logo !== '') {
        $('#logoProveedor').html('<img src="../storage/app/logo/' + json.logo + ' />');
      }
      else {
        $('#logoProveedor').html('<i class="fas fa-user-tie fa-10x" style="color:#91B2F3;"></i>');
      }
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
          $("#debe").html(html).trigger("change");
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
              $("#haber").html(html).trigger("change");
            },
          });
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
  window.location.href = "v3/" + $("#controlador").val() + "/proveedor?id=" + id;
}
function validarCodigo() {
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
        } else {
          mensajes("question", $("#pregunta").val());
        }
      }
    },
    error: function () { },
  });
}
function verRetencion(id, num) {
  $("#titulo_modal_retenciones_proveedores").html('<i class="fas fa-edit"></i>Retención No.: ' + num);
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
function agregarNotadedebito() {
  window.open("v3/" + $("#controlador").val() + "/nuevanotadedebito", "_self");
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
      $("#tipo").val(json.tipo);
      $("#numero").val(json.numero);
      $("#proveedor").val(json.razonsocial);
      $("#observacion").val(json.observacion);
      $("#autorizacion").val(json.autorizacion);
      $("#subtotal").val(json.subtotal);
      $("#iva_total").val(json.iva_total);
      $("#valor_contable").val(json.valor_contable);
      $("#total_final").val(json.total_final);

      // Nuevo

      $("#valorBase").val(parseFloat(parseFloat(json.subtotal_0) + parseFloat(json.subtotal_12)).toFixed(2));
      $("#ivaBase").val(json.iva_total);
      agregarDetCompra(); // HACER VALIDACION ESTO SE USA EN LA NOTA DE CREDITO PROVEEDOR
    },
  });
}
function obtenerFacturaProveedor() {
  //console.log("aquii");
  var d = $("#id_proveedor").val().split("|");
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/obtenerFacturaProveedor",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: d[0],
      //id: $("#id_proveedor").val(),
    },
    datatype: "html",
    success: function (html) {
      //console.log(html);
      $("#consolidado_facturas").html(html);
    },
    error: function (json) {
      console.log(json);
    },
  });
}
function agregarImpuesto() {
  if ($("#facturas").val() != "") {
    filaImpuesto++;
    var html = "";
    html += '<tr id="tr_' + filaImpuesto + '">';
    html += '<td><select class="form-control" style="width: 100%;" id="tipo_retencion' + filaImpuesto + '" name="tipo_retencion[]" onchange="traerPorcentaje(\'' + filaImpuesto + '\')"><option value="">--Seleccione--</option><option value="2">Renta</option><option value="1">Iva</option></select></td>';
    html += '<td><select class="form-control select2_porcentaje" style="width: 100%;" id="porcentaje_retencion' + filaImpuesto + '" name="porcentaje_retencion[]" onchange="asignarCodigo(\'' + filaImpuesto + '\')"><option value="">--Seleccione--</option></select></td>';
    html += '<td><input type="hidden" id="codigo_retencion' + filaImpuesto + '" name="codigo_retencion[]" /><input type="text" class="form-control text-right" id="codigo_retencion_' + filaImpuesto + '" name="codigo_retencion_[]" placeholder="-" readonly /></td>';
    html += '<td><input type="text" class="form-control text-right" id="base_retencion' + filaImpuesto + '" name="base_retencion[]" placeholder="0.00" readonly /></td>';
    html += '<td><input type="text" class="form-control text-right" id="valor_retencion' + filaImpuesto + '" name="valor_retencion[]" placeholder="0.00" readonly /></td>';
    html += '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarImpuesto(\'' + filaImpuesto + '\');"><i class="fa fa-trash-alt"></i></a></td>';
    html += "</tr>";
    $("#impuestos").append(html);
    $("#tipo_retencion" + filaImpuesto).select2();
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
      $("#detalle_compra").html(html);
    },
    error: function (json) {
      console.log(json);
    },
  });
}
function agregarRubrosNd() {
  var Rubro = "";
  var json = ajax(
    "v3/" + $("#controlador").val() + "/getAllRubros",
    null,
    "json"
  );
  if (json.responseJSON != "") {
    json = json.responseJSON;
    $.each(json.rubros, function (i, item) {
      Rubro += '<option value="' + item.id + '">' + item.nombre + "</option>";
    });
    filaRubros++;
    var html = "";
    html += '<tr id="tr_' + filaRubros + '">';
    html +=
      '<td ><select class="form-control select2_" id="id_pago_' +
      filaRubros +
      ' name="id_rubro[]"><option value="">--Seleccione--</option>' +
      Rubro +
      "</select></td>";
    html +=
      '<td > <input type="checkbox" name="kardex" checked data-bootstrap-switch data-off-color="danger" data-on-color="success"> <input type="checkbox"  id="check_iva' +
      filaRubros +
      '" name="check_iva[]"   /></td>';
    html +=
      '<td ><input type="text" class="form-control text-right" id="detalle_rubro' +
      filaRubros +
      '" name="detalle_rubro[]" placeholder="ingrese el detalle" /></td>';
    html +=
      '<td ><input type="text" class="form-control text-right" id="divisa_rubro' +
      filaRubros +
      '" name="divisa_rubro[]" value="USD" placeholder="-" readonly /></td>';
    html +=
      '<td ><input type="text" class="form-control text-right" id="valor_rubro' +
      filaRubros +
      '" name="valor_rubro[]" value="0.00" placeholder="0.00" onchange="calcula_valor_rubros_nd(' +
      filaRubros +
      ')" /></td>';
    html +=
      '<td ><input type="text" class="form-control text-right" id="total_rubro' +
      filaRubros +
      '" name="total_rubro[]" value="0.00" placeholder="0.00" /></td>';
    html +=
      '<td ><a href="javascript:;" class="btn btn-danger" onclick="eliminarRubros(\'' +
      filaRubros +
      '\');"><i class="fa fa-trash-alt"></i></a></td>';
    html += "</tr>";
    $("#rubros").append(html);
    $(".select2_").select2();
    $("input[data-bootstrap-switch]").each(function () {
      $(this).bootstrapSwitch("state", $(this).prop("checked"));
    });
  }
}
function eliminarRubros(id) {
  $("#tr_" + id).remove();
  filaRubros--;
  calcularRetencion();
}
function calcula_valor_rubros_nd(id) {
  var total = parseFloat($("#total_rubro" + id).val());
  var abono = parseFloat($("#valor_rubro" + id).val());

  console.log($("#valor" + id).val(abono.toFixed(2, 2)));
  $("#valor_rubro" + id).val(parseFloat(abono).toFixed(2));
  $("#total_rubro" + id).val(parseFloat(abono).toFixed(2));
  suma_total_valor_rubro();
  if ($("#check_iva" + id).is(":checked")) {
    console.log("aquii");
  }
}
function suma_total_valor_rubro() {
  total_rubros = 0;
  cantidad = 0;
  subtotal_rubros = 0;
  impuesto_rubros = 0;

  impuestoIva = $("#impuestoIva").val();
  $("input[name='valor_rubro[]']").each((i, v) => {
    //console.log($("#valor_rubro" + i));
    cantidad = parseFloat($("#valor_rubro" + i).val());
    //console.log(cantidad);
    if (!isNaN(cantidad)) {
      //console.log(impuestoIva);
      subtotal_rubros += cantidad;
      impuesto_rubros += parseFloat(impuestoIva) * parseFloat(cantidad);
      total_rubros = subtotal_rubros + impuesto_rubros;
    }
  });
  if (isNaN(total)) {
    total = 0;
  }
  $("#subtotalrubro").val(subtotal_rubros.toFixed(2, 2));
  $("#impuestorubro").val(impuesto_rubros.toFixed(2, 2));
  $("#sumtotalrubro").val(total_rubros.toFixed(2, 2));
}
function valida_total_abono_nd(total, element) {
  var abono = element.value;
  console.log(total, abono);
  if (total >= abono) {
    //total_detalle_compra();
  } else {
    mensajes(
      "error",
      "El abono " + abono + " excede el total del detalle de la compra"
    );
    element.value = total;
    element.style.backgroundColor = "#F1948A";
  }
}
function valida_total_abono_nc(total, element) {
  var abono = element.value;
  //console.log(total, abono);
  if (total >= abono) {
    total_detalle_compra();
  } else {
    mensajes(
      "error",
      "El abono " + abono + " excede el total del detalle de la compra"
    );
    element.value = total;
    element.style.backgroundColor = "#F1948A";
  }
}
function total_detalle_compra() {
  var tabla = document.getElementById("tabla_compra");
  var checkbox = document.getElementsByName("ch[]");
  var subtotal0 = document.getElementsByName("subtotal0[]");
  var subtotal12 = document.getElementsByName("subtotal12[]");
  var subtotal_total = document.getElementsByName("subtotal_total[]");
  var impuesto = document.getElementsByName("val_impuesto[]");
  var total = document.getElementsByName("total[]");
  var id_detalle = document.getElementsByName("id_detalle[]");
  var abono = document.getElementsByName("abono[]");
  var porcentaje_iva = document.getElementsByName("porcentaje_iva[]");


  console.log(checkbox);
  //console.log(subtotal0, checkbox);
  var acum0 = 0;
  var acum12 = 0;
  var acumsubtotal = 0;
  var acumimpuesto = 0;
  var acumtotal = 0;
  var cantidad_check = 0;
  var arr_id_det = [];
  var arr_row = [];
  var valor_iva = 0;
  //console.log(tabla.rows.length);
  for (var i = 0; i < tabla.rows.length - 3; i++) {
    // Obtener el checkbox de la fila actual

    //console.log(checkbox[i]);
    // Verificar si el checkbox está marcado

    if (checkbox[i].checked) {
      cantidad_check++;
      //console.log(cantidad_check);
      //console.log("Checkbox en la fila " + (i + 1) + " está marcado.");
      if (impuesto[i].value > 0) {
        acum12 = parseFloat(acum12) + parseFloat(abono[i].value);
        valor_iva = parseFloat(abono[i].value) * parseFloat(porcentaje_iva[i].value);
      } else {
        acum0 = parseFloat(acum0) + parseFloat(abono[i].value);
        //alert("aqui 2 ");
      }

      //acumsubtotal = parseFloat(acumsubtotal) + parseFloat(subtotal_total[i].value);
      acumsubtotal = parseFloat(acum12) + parseFloat(acum0);
      //acumimpuesto = parseFloat(acumimpuesto) + parseFloat(impuesto[i].value);
      acumimpuesto = parseFloat(acumimpuesto) + parseFloat(valor_iva);
      //acumtotal = parseFloat(acumtotal) + parseFloat(total[i].value);
      acumtotal =
        parseFloat(acum12) + parseFloat(acum0) + parseFloat(acumimpuesto);
      arr_id_det.push(id_detalle[i].value);
      arr_row.push(i);

      //$('#codigo').val(codigo);
      $("#subtotal0").val(acum0);
      $("#subtotal12").val(acum12);
      $("#subtotal_total").val(acumsubtotal);
      $("#impuesto").val(parseFloat(acumimpuesto.toFixed(2)));
      $("#total").val(acumtotal);
      $("#cantidad_ok").val(cantidad_check);
      $("#a_detalles").val(arr_id_det);
      $("#a_row").val(arr_row);
    } else {
      //acumtotal = 0;
      $("#subtotal0").val(acum0);
      $("#subtotal12").val(acum12);
      $("#subtotal_total").val(acumsubtotal);
      $("#total").val(acumtotal);
      $("#impuesto").val(parseFloat(acumimpuesto.toFixed(2)));
      //acum0 = 0;
      //console.log("Checkbox en la fila " + (i + 1) + " no está marcado.");
    }
    //console.log(arr_id_det);
    //console.log(acum0);
    console.log(acumtotal);
  }
}
function verNotadeCredito(id, num) {
  $("#titulo_modal").html('<i class="fas fa-edit"></i> No.: ' + num);
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getNotadeCreditoPrint",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "html",
    success: function (html) {
      html = html.split("|");
      $("#contenidoHtml").html(html[0]);
      $("#titulo_modal").html(
        '<i class="fas fa-eye" style="color:cyan;"></i> Nota de crédito No.:' +
        html[1]
      );
      $("#myModal").modal("toggle");
    },
    error(e) {
      //window.open("v3/admin", "_self");
      console.log(e);
    },
  });
}
function verNotadeDebito(id, num) {
  $("#titulo_modal").html('<i class="fas fa-edit"></i> No.: ' + num);
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getNotadeDebitoPrint",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "html",
    success: function (html) {
      html = html.split("|");
      $("#contenidoHtml").html(html[0]);
      $("#titulo_modal").html(
        '<i class="fas fa-eye" style="color:cyan;"></i> Nota de débito No.:' +
        html[1]
      );
      $("#myModal").modal("toggle");
    },
    error(e) {
      //window.open("v3/admin", "_self");
      console.log(e);
    },
  });
}
function anularNotaCredito(id) {
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
      testnota(id);
    }
  });
}
async function testnota(id) {
  try {
    const { value: text } = await Swal.fire({
      input: "textarea",
      inputPlaceholder: "Ingrese motivo de anulación...",
      inputAttributes: {
        "aria-label": "Ingrese motivo de anulación",
      },
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Anular",
    });

    if (text) {
      console.log($("#swal2-textarea").val());
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/anularNotaCredito",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          observacion: $("#swal2-textarea").val(),
        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, "", "", "", "tabla");
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
          console.log(registro);
          $("#porcentaje_retencion" + fil).append('<option value=" ' + registro.id + "|" + registro.nombre + "|" + registro.valor + '">' + registro.codigo + " | " + registro.nombre + " | " + registro.valor + "</option>");
        });
        if ($("#tipo_retencion" + fil).val() == "RENTA") {
          $("#base_retencion" + fil).val($("#subtotal").val());
        } else {
          $("#base_retencion" + fil).val($("#iva_total").val());
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

  var d = $("#porcentaje_retencion" + fil).val().split("|");
  if ($('#tipo_retencion' + fil).val() == 2) {
    $('#base_retencion' + fil).val($("#valorBase").val());
    var valorRetencion = (parseFloat(d[2]) * parseFloat($("#valorBase").val())) / 100;
  } else {
    $('#base_retencion' + fil).val($("#ivaBase").val());
    var valorRetencion = (parseFloat(d[2]) * parseFloat($("#ivaBase").val())) / 100;
  }

  $("#codigo_retencion_" + fil).val(d[2]);
  $("#codigo_retencion" + fil).val(d[0]);
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

        if (d == 2) {
          renta += parseFloat($("#valor_retencion" + to).val());
        } else if (d == 1) {
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
        mensajes(
          "question",
          $("#pregunta").val(),
          "frm_comprobante",
          "action",
          // "v3/proveedores/comprobantedeegreso"
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
          // "v3/proveedores/comprobantedeegreso"
        );
        document.getElementById("btnguardarComprobante").disabled = false;
      }
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
function verComprobantevarios(id) {
  window.open(
    $("#base_url").val() +
    "/v3/" + $('#controlador').val() + "/crearComprobanteEgresovarios?id=" +
    id,
    "_self"
  );
}
function verCruce(id_Cruce) {
  window.open(
    $("#base_url").val() + "/v3/proveedores/crearCruceValor?id=" + id_Cruce,
    "_self"
  );
}
function regresar(ruta) {
  console.log($("#base_url").val() + ruta);
  Swal.fire({
    title: "¿Desea regresar a la pantalla anterior?",
    text: $("#informa").val(),
    icon: "info",
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = $("#base_url").val() +'/'+ ruta;
    }
  });
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
      order: [[0, "desc"]],
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
function buscarComprobanteEgreso() {
  if ($("#desde").val() == "") {
  } else if ($("#hasta").val() == "") {
    return mensajes("warning", "Debe ingresar la fecha de final...");
  } else {
    $("#tableComprobanteEgresoVarios").dataTable().fnClearTable();
    $("#tableComprobanteEgresoVarios").dataTable().fnDestroy();
    $("#tableComprobanteEgresoVarios").DataTable({
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
        url: "v3/" + $("#controlador").val() + "/" + "allComprobantesVarios",
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
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento_cabecera + '\')">' + row.id_asiento_cabecera + '</a>';
          }
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
          data: "nro_cheque",
        },
        {
          data: "id_proveedor",
        },
        {
          data: "descripcion",
        },
        {
          className: "text-right",
          data: "valor",

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
              $("#activo").val();
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
              ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verComprobantevarios(\'' +
              row.id +
              '\');"><i class="fas fa-eye" style="color:green;"></i> ' +
              $("#ver").val() +
              (row.estado != 0
                ? '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoEgresoVario(\'' +
                row.id +
                "','" +
                row.estado +
                "','ComprobanteEgreso');\">" +
                accion
                : "") +
              "</a><a class='dropdown-item' href='javascript:;' onclick=\"mostrarPDFingreso('" + row.id + "');\"><i class='fas fa-file-pdf' style='color:gray;'></i> PDF</a></div></div>";
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
      order: [[0, "desc"]],
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
                ? '<label class="text-success">' +
                $("#activo").val() +
                "</label>"
                : '<label class="text-danger">' +
                $("#activo").val() +
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
    title: "¿Desea Anular este cruce?",
    text: $("#informa").val(),
    icon: "info",
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      testCruce(id);
    }
  });
}
async function testCruce(id) {
  try {
    const { value: text } = await Swal.fire({
      input: "textarea",
      inputPlaceholder: "Ingrese motivo de anulación...",
      inputAttributes: {
        "aria-label": "Ingrese motivo de anulación",
      },
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Anular",
    });

    if (text) {
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/estadoCruceValor",
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
          mensajes(json.status, json.msj, "", "", "", "tabla");
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
            $("#");
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

      if (
        Number($("#saldo" + contador).val()) >=
        Number($("#abono" + contador).val())
      ) {
        let tot =
          Number($("#saldo" + contador).val()) -
          Number($("#abono" + contador).val());
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

  var valor = parseFloat($("#valor").val());
  var abono = parseFloat($("#abono" + id).val());
  var saldo = parseFloat($("#saldo" + id).val());

  console.log(valor, abono, saldo);
  var cantidad = parseFloat($("#total_suma").val());
  if (!isNaN(abono) && !isNaN(valor) && !isNaN(cantidad)) {
    console.log("calculo:", cantidad, valor);
    if (cantidad < valor) {
      if (valor <= abono) {
        $("#abono" + id).val(parseFloat(0).toFixed(2));
        mensajes("error", "No se puede superar el valor inicial");
        return;
      }
      $("#verificar_superavit").val(1);
      if (abono > saldo) {
        abono = saldo;
      }
      let totalO = parseFloat(saldo - abono);
      $("#nuevo_saldo" + id).val(totalO);
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
    $("#abono" + id).val(valor.toFixed(2, 2));
  }
  sumaTotalesF();
}
$("#guardarCruceValores").on("click", function () {
  let tabla = document.querySelector("#tablaValores tbody");
  let cant = tabla.querySelectorAll("tr").length;
  let tablaUno = document.querySelector("#tableDeudasProveedor tbody");
  let cantUno = tablaUno.querySelectorAll("tr").length;
  let vali1 = cant - 1;
  let vali2 = cantUno - 1;
  if ($("#proveedor").val() == "") {
    return mensajes("warning", "Debe ingresar el proveedor...");
  } else if ($("#concepto").val() == "") {
    return mensajes("warning", "Debe ingresar el concepto...");
  } else if ($("#sucursalc").val() == "") {
    return mensajes("warning", "Debe ingresar la sucursal...");
  } else if ($("#abono_a" + vali1).val() == "") {
    for (let index = 0; index < cant; index++) {
      if ($("#abono_a" + index).val() == "") {
        return mensajes(
          "warning",
          `Debe ingresar el ebono en la final ${index + 1
          } en la tabla de valores a favor...`
        );
      }
    }
  } else if ($("#abono" + vali2).val() == "") {
    for (let index = 0; index < cantUno; index++) {
      if ($("#abono" + index).val() == "") {
        return mensajes("warning", `Debe ingresar el ebono en la final ${index + 1} en la tabla de deudas del proveedor...`);
      }
    }
  } else {
    mensajes("question", $("#pregunta").val(), "frm_cruceValores", "action", "");
  }
});
function mostrarPDFCruce(id) {
  window.open("v3/" + $("#controlador").val() + "/mostrarPddCruce?id=" + id, "_blank");
}
function mostrarPDFingreso(id) {
  window.open("v3/" + $("#controlador").val() + "/mostrarPdfIngreso?id=" + id, "_blank");
}
function agregarCruceCuenta() {
  window.open("v3/" + $("#controlador").val() + "/nuevo_cruce_cuentas", "_self");
}
$("#agregarLineaCruceProv").on("click", function () {
  var html = '<tr id="trp_' + filaCrucep + '">';
  html += '<td style="max-width: 100%;"><select class="form-control select2_cuenta" id="id_cuentap_' + filaCrucep + '" name="cuentap[]"><option value="">--Seleccione--</option></select></td>';
  html += '<td><input type="text" class="form-control " id="detallep_' + filaCrucep + '" name="detallep[]" value="" " /></td>';
  html += '<td><input type="text" class="form-control " id="divisap_' + filaCrucep + '" name="divisap[]" value="USD" " readonly/></td>';
  html += '<td><input type="text" class="form-control text-right" id="valorp_' + filaCrucep + '" name="valorp[]" value="0.00" onchange="valor_rubroCrucep(' + filaCrucep + ')" "/></td>';
  html += '<td><input type="text" class="form-control text-right" id="total_basep_' + filaCrucep + '" name="total_basep[]" value="0.00" " readonly/></td>';
  html += '<td><a href="javascript:;" class="btn btn-danger" onclick="eliminarFilap(\'' + filaCrucep + '\');"><i class="fa fa-trash-alt"></i></a></td>';
  html += "</tr>";
  $("#detalleCruceCuentasprov").before(html);
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
  filaCrucep++;
});
function eliminarFilap(filaCrucep) {
  $("#trp_" + filaCrucep).remove();
  filaCrucep--;
  sumarCruceProv();
}
function valor_rubroCrucep(id) {
  var e = parseFloat($("#valorp_" + id).val());
  if (e == "" || isNaN(e) || e <= 0) {
    e = 0;
  }
  $("#valorp_" + id).val(parseFloat(e).toFixed(2, 2));
  $("#total_basep_" + id).val(parseFloat(e).toFixed(2, 2));
  sumarCruceProv(id);
}
function sumarCruceProv(id) {
  var valorp = 0;
  var totalp = 0;
  $("input[name='valorp[]']").each((i, v) => {
    valorp += Number(v.value);
  });
  totalp = totalp + valorp;
  total_finalp = parseFloat(totalp.toFixed(2, 2));
  if ($("#info_valor").val() >= total_finalp) {
    if (!isNaN(totalp)) {
      $("#totalp").val(totalp.toFixed(2));
    }
  } else {
    $("#valorp_" + id).val(parseFloat(0).toFixed(2, 2));
    $("#total_basep_" + id).val(parseFloat(0).toFixed(2, 2));
    sumarCruceProv(id);
    return mensajes(
      "warning",
      "El total no dede ser mayor al valor de la factura"
    );
  }
}
function verCruceCuentas(id_cruce) {
  limpiarModal();
  var tablaHtml = "";
  $("#titulo_modal").html(
    '<i class="fas fa-eye"></i> Visualizador Cruce de Cuentas Proveedores'
  );
  var data = {
    id: id_cruce,
  };
  var json = ajax(
    "v3/" + $("#controlador").val() + "/getCruceCuentas",
    data,
    "json"
  );
  if (json.responseJSON != null) {
    json = json.responseJSON;
    $("#id").val(json.id);
    $("#numero").val(json.secuencia);
    $("#asiento").val(json.id_asiento_cabecera);
    $("#fecha").val(json.fecha);
    $("#fecha_fact").val(json.fecha_fact);
    $("#concepto").val(json.detalle);
    $("#info").val(json.total);
    $("#comprobante").val(json.comprobante);
    $("#proveedorcc").val(json.proveedor);
    $("#tablaDetallesProv").empty();

    var dataCruce = {
      id: id_cruce,
    };
    var cruce = ajax(
      "v3/" + $("#controlador").val() + "/getDetalleCruceCuentas",
      dataCruce,
      "json"
    );
    if (cruce.responseJSON != null) {
      var detalleJson = cruce.responseJSON;
      var total = 0;
      if (detalleJson != null && detalleJson.length > 0) {
        tablaHtml = "<tr>";
        for (var i = 0; i < detalleJson.length; i++) {
          tablaHtml +=
            '<td style="width: 30%;">' +
            detalleJson[i].codigo +
            " | " +
            detalleJson[i].nombre +
            '</td><td style="width: 40%;">' +
            detalleJson[i].observaciones +
            '</td><td style="width: 10%;"> USD </td><td style="width: 10%; text-align: right;">' +
            detalleJson[i].total +
            '</td><td style="width: 10%; text-align: right;">' +
            detalleJson[i].total +
            "</td></tr>";
          total += parseFloat(detalleJson[i].total) || 0;
        }
        tablaHtml += "</tbody></table>";
        $("#total").val(total.toFixed(2, 2));
        $("#tablaDetallesProv").html(tablaHtml);
      }
    }
    $("#myModal").modal("toggle");
  }
}
function anularCruceProv(id) {
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
      testp(id);
    }
  });
}
async function testp(id) {
  try {
    const { value: text } = await Swal.fire({
      input: "textarea",
      inputPlaceholder: "Ingrese motivo de anulación...",
      inputAttributes: {
        "aria-label": "Ingrese motivo de anulación",
      },
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Anular",
    });

    if (text) {
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/anularCruceProv",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
        },
        datatype: "json",
        success: function (json) {
          mensajes(json.status, json.message, "", "", "", "tabla");
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
function mostrarPDFCruceProv(id) {
  window.open(
    "v3/" + $("#controlador").val() + "/mostrarPDFCruceCuentaProv?id=" + id,
    "_blank"
  );
}
function buscarRetencionesProveedores() {
  $("#tableRetenciones").dataTable().fnClearTable();
  $("#tableRetenciones").dataTable().fnDestroy();
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
    order: [[0, "desc"]],
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
          var estado = '<label class="text-success">' + $("#activo").val() + "</label>";
          if (row.estado == 0) {
            estado = '<label class="text-danger">' + $("#inactivo").val() + "</label>";
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
          var estado = '<img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:15px;" title="Sin Autorizar" />';
          if (row.doc_electronico == 5) {
            estado = '<img src="' + $("#base_url").val() + '/v3/dist/img/puntoverde.png" style="width:20px;" title="Autorizado" />';
          } else if (row.doc_electronico == 3) {
            estado = '<img src="' + $("#base_url").val() + '/v3/dist/img/puntoplomo.png" style="width:20px;" title="Autorizado" />';
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
            accion = '<i class="fas fa-times-circle" style="color:red;"></i> Anular';
          }
          var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verRetencion(\'' + row.id + "','" + row.nro_comprobante + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + ' </a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoRetencionesP(\'' + row.id + "','" + row.estado + "','Retenciones');\">" + accion + "</a></div></div>";
          return botones;
        },
      },
    ],
  });
}
function cambiarEstadoRetencionesP(id) {
  Swal.fire({
    title: "¿Desea Anular esta retención?",
    text: $("#informa").val(),
    icon: "info",
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "get",
        url: "v3/" + $("#controlador").val() + "/verificarAnulacion",
        datatype: "json",
        data: {
          verificar: "3",
          id_retencion: id,
        },
        success: function (data) {
          if (data.respuesta == "error") {
            mensajes("error", "Error", data.msj);
          } else {
            if (data.respuesta == "si") {
              // cambiar a modulos de v3
              let enlace = `<a target="_blank" href="{{ url('contable/acreedores/documentos/comprobante/egreso/comprobante/buscar?id=${data.ids[0]}')}}"><b>${data.tablas[0]}</b></a>`;
              let enlace2 = `<a target="_blank" href="{{ url('contable/Banco/debito/acreedores/buscar?id=${data.ids[1]}')}}"> <b>${data.tablas[1]}</b> </a>`;
              let enlace3 = `<a target="_blank" href="{{ url('contable/cruce/valores?id=${data.ids[2]}')}}"><b>${data.tablas[2]}</b></a>`;
              let texto = `trans('contableM.existenalgunos') ${enlace} ${enlace2} ${enlace3} trans('contableM.generadosconestafac')`;
              mensajes("error", data.msj, '', '', '', '', data.footer);
            } else {
              if (data.status == "error") {
                return mensajes("warning", data.msj);
              } else {
                testRetenciones(id);
              }
            }
          }
        },
        error: function (data) {
          console.log(data);
        },
      });
    }
  });
}
async function testRetenciones(id) {
  try {
    const { value: text } = await Swal.fire({
      input: "textarea",
      inputPlaceholder: "Ingrese motivo de anulación...",
      inputAttributes: {
        "aria-label": "Ingrese motivo de anulación",
      },
      showCancelButton: true,
    });
    if (text) {
      $.ajax({
        type: "get",
        url: "v3/" + $("#controlador").val() + "/anularRetencion",
        datatype: "json",
        data: {
          id: id,
          concepto: text,
        },
        success: function (data) {
          console.log(data);
          if (data.status == "success") {
            $('.tablaDatos').DataTable().ajax.reload();
            return mensajes("success", "Anulación Correcta");
          } else {
            return mensajes("error", data.msj);
          }
        },
        error: function (data) {
          console.log(data);
        },
      });
    }
  } catch (err) {
    console.log(err);
  }
}
function isNumberKey(evt) {
  var charCode = (evt.which) ? evt.which : evt.keyCode;
  if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode != 46)
    return false;
  return true;
}
function addvalue() {
  var totalDebe = 0;
  $(".debe").each(function () {
    if ($(this).val().length > 0) {
      totalDebe = parseFloat(totalDebe) + parseFloat($(this).val());
    }
  });
  totalDebe = parseFloat(totalDebe).toFixed(2);
  $("#debe_final").val(totalDebe);
  return totalDebe;
}
function addvalue2() {
  let valor = document.getElementById('valor').value;
  var totalHaber = valor;
  $(".haber").each(function () {
    if ($(this).val().length > 0) {
      totalHaber = parseFloat(totalHaber) + parseFloat($(this).val());
    }

  });
  totalHaber = parseFloat(totalHaber).toFixed(2);
  $("#haber_final").val(totalHaber);
  return totalHaber;
}
function eliminarFila(fila) {
  $("#tr_" + fila).remove();
  addvalue();
  addvalue2();
}
$("#agregarlineacomprobante").on("click", function () {

  var html = '<tr id="tr_' + fila + '">';
  html += '<td><select class="form-control select2_cuenta" id="cuenta_' + fila + '" name="cuenta[]" required><option value="">--Seleccione--</option></select> </td>';
  html += '<input type="hidden" id="visibilidad' + fila + '" name="visibilidad[]" value="1">';
  html += '<td><select class="form-control select2_divisa" id="divisa_' + fila + '" name="divisa[]"><option value="">--Seleccione--</option></select></td>';
  html += '<td><input type="text" class="form-control text-right debe" id="debe_' + fila + '" name="debe[]" value="0.00" onkeypress="return isNumberKey(event)" onblur="this.value = parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : \'0.00\'; addvalue();" /></td>';
  html += '<td><input disabled type="text" class="form-control text-right haber" style="width: 88%; height: 80%" id="haber_' + fila + '" name="haber[]" onkeypress="return isNumberKey(event)" onblur="this.value = parseFloat(this.value) > 0 ? parseFloat(this.value).toFixed(2) : \'0.00\'; addvalue2();" value="0.00" /></td>';
  html += '<td><input type="text" class="form-control text-right" id="valor_base_' + fila + '" name="valor_base[]" value="0.00" /></td>';
  html += '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(' + fila + ')"></i></td>';
  html += '</tr>';
  $("#detallecomporbante").before(html);
  $(".select2_divisa").select2({
    placeholder: "Seleccione una bodega",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayDivisas').val()), function (method) {
      return {
        text: method.descripcion,
        id: method.id,
      }
    }))
  });
  $(".select2_cuenta").select2({
    placeholder: "Seleccione una cuenta",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayCuenta').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      }
    }))
  });
  fila++;
});
function cambiarFecha(e) {
  $("#fecha_cheque").val(e.value);
}
$("#banco").on("change", function () {
  var data = {
    idBanco: this.value
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getLastNumberChe", data, "json");
  console.log(json.responseJSON);
  if (json.responseJSON !== null) {
    json = json.responseJSON;
    if (json.no_cheque !== null && json.no_cheque !== '') {
      $("#num_cheque").val(Number(json.no_cheque) + 1);
    }
  }
});
function cambiarEstadoEgresoVario(id, estado) {
  Swal.fire({
    title: '¿Desea Anular este comprobante de egreso varios?',
    text: 'No puedes revertir esta acccion!',
    icon: 'error',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: "Si"
  }).then((result) => {
    if (result.isConfirmed) {
      testComprobanteEgresoVarios(id, estado);
    }
  })
}
async function testComprobanteEgresoVarios(id, estado) {
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
        url: "v3/" + $('#controlador').val() + "/anularComproEgresoVarios",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          text: text,
          estado: estado
        },
        datatype: "json",
        success: function (data) {
          if (data.status == 'success') {
            mensajes('success', data.msj, '', '', '', 'tabla');

          } else {
            mensajes('error', data.msj);
          }
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

function anularNotaDebito(id) {
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
      testnota(id);
    }
  });
}
async function testnota(id) {
  try {
    const { value: text } = await Swal.fire({
      input: "textarea",
      inputPlaceholder: "Ingrese motivo de anulación...",
      inputAttributes: {
        "aria-label": "Ingrese motivo de anulación",
      },
      showCancelButton: true,
      confirmButtonColor: "#3085d6",
      cancelButtonColor: "#d33",
      confirmButtonText: "Anular",
    });

    if (text) {
      console.log($("#swal2-textarea").val());
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/anularNotaDebito",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          observacion: $("#swal2-textarea").val(),
        },
        datatype: "json",
        success: function (json) {
          console.log(json)
          mensajes(json.status, json.msj, "", "", "", "tabla");
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


$("#btnactualizarComprobanteEgresoVarios").on("click", function () {
  mensajes("question", $("#pregunta").val(), "frm_comprobanteegresovarios", "action", "v3/proveedores/comprobantedeegresovario");
});