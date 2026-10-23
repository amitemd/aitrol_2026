var fila = 0;
var filaPago = 0;
var totalEfectivo = 0;
var totalCheque = 0;
var totalTarjeta = 0;
var arregloVerificar = [];
$(document).ready(function () {
  $('#modal_conglomerada').on('shown.bs.modal', function (e) {
    verificarCheck();
  });

  $('#modal_conglomerada').on('hidden.bs.modal', function (e) {
    $("#tipo_documento").val("").trigger("change");
  });

  $('#examples2').DataTable({
    "searching": false,
    "paging": false,
    "responsive": true,
    "lengthMenu": [10, 25, 50],
    "pageLength": 10,

  });


  if ($("#modR").val() == 1) {
    let option = $("#identificacion_edit").val();
    let option1 = $("#razon_social_edit").val();
    let newOption = new Option(option, option, false, false);
    let newOption1 = new Option(option, option, false, false);
    $('#ppp').append(newOption).trigger('change');
    $('#identi_paciente').append(newOption1).trigger('change');
    totalesRC(0);
  }
  $("#id_proveedor_ventas").select2({
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
  $(".select2_producto_ventas").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarProducto",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {

        const results = response.map(item => ({
          id: item.id,
          text: item.text,

        }));

        return {
          results: results
        };
      },
      error(error) {
        ""
        console.log(error + " asds");
      },
      cache: true,
    },
  });
  $(".select2_producto_fv").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarProducto",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {

        const results = response.map(item => ({
          id: item.id,
          text: item.nombre,
          data: {
            name: item.nombre,
            codigo: item.codigo,
            descuento: item.mod_desc,
            precio: item.mod_precio,
            maxdesc: item.descuento,
            iva: item.iva
          }
        }));

        return {
          results: results
        };
      },
      error(error) {
        ""
        console.log(error + " asds");
      },
      cache: true,
    },
  });
  if ($("#submodulo_").val() == "facturar" && $('#extra_').val() == 'facturaventa') {
    $(".botonMenu").trigger("click");
  }
  $("#elementTour").val(
    "hero-factura,hero-datosfactura,hero-cliente,hero-paciente,hero-seguro,hero-agregarlinea,hero-agregarcliente"
  );
  $(".js-data-identi").select2({
    minimumInputLength: 2,
    cache: true,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarCliente",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (data) {
        return {
          results: $.map(data, function (item) {
            let data = {
              id: item.id,
              text: item.text.split(" ")[0],
              data: {
                identicacion: item.id.split("|")[0],
                telefono1: item.id.split("|")[1],
                telefono2: item.id.split("|")[2],
                direccion: item.id.split("|")[3],
                email: item.id.split("|")[4],
                nombre: item.id.split("|")[5],
                pais: item.id.split("|")[6],
              },
            };
            console.log(data);
            return data;
          }),
        }
      },
    },
    language: {
      inputTooShort: function () {
        return "Por favor ingrese 2 caracteres....";
      },
    },
  });
  $(".js-data-identi").on("change", function (e) {
    let selectedOption = $(this).select2("data")[0].data;
    $("#identificacion_edit").val(selectedOption.identicacion);
    $("#nombre_cliente").val(selectedOption.nombre);
    $("#direccion_cliente").val(selectedOption.direccion);
    $("#ciudad_cliente").val(selectedOption.pais);
    $("#mail_cliente").val(selectedOption.email);
    $("#telefono_cliente").val(selectedOption.telefono1 ?? selectedOption.telefono2 ?? "");
  });
  $(".js-data-paciente").select2({
    minimumInputLength: 2,
    cache: true,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarPaciente",
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
              id: item.id,
              text: item.text.split("|")[1],
            };
          }),
        };
      },
    },
    language: {
      inputTooShort: function () {
        return "Por favor ingrese 2 caracteres....";
      },
    },
  });
  $(".js-data-paciente").on("change", function (e) {
    let selectedOption = $(this).select2("data")[0];
    let data = selectedOption.id.split("|");
    $("#nombre_paciente").val(selectedOption.text);
    $("#identificacion_paciente").val(data[0]);
  });
  try {
    let idConglomerada = $("#id").val();
    if (idConglomerada) {
      $("#sucursal_empre").trigger("change");
      let idCliente = $("#idClienteEd").val();
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/buscarCliente",
        headers: {
          "X-CSRF-TOKEN": $("input[name=_token]").val(),
        },
        type: "GET",
        data: {
          term: idCliente
        },
        dataType: "json",
        success: function (selectedOption) {
          let data = selectedOption[0].id.split("|");
          $("#nombre_cliente").val(selectedOption[0].text.split("|")[1]);
          $("#direccion_cliente").val([data[3]]);
          $("#ciudad_cliente").val(data[2]);
          $("#mail_cliente").val(data[4]);
          $("#telefono_cliente").val(data[1]);
        },
        error: function (xhr, status) {
          alert("Existió un problema");
        },
      });
    }
    $("#tableVentas").DataTable({
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
      "order": [
        [6, 'desc'], // Ordena por la primera columna (fecha) en orden descendente
        [0, 'desc'] // Luego ordena por la segunda columna (comprobante) en orden descendente
      ],
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
          exportOptions: {
            columns: ':not(:eq(14))' // Excluir la columna en el índice 2
          }
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
          data: "nro_comprobante",
        },
        {
          data: "nombre_cliente",
        },
        {
          className: "text-center",
          data: "id",
        },
        {
          className: "text-center",
          data: "id_asiento",
          render: function (data, type, row) {
            return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\')">' + row.id_asiento + '</a>';
          }
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = "";
            if (row.doc_electronico == -1) {
              estado = '<a href="#" title="Pendiente"><i class="fa-solid fa-circle" style="color:yellow;width:16px;"></i></a>';
            } else if (row.doc_electronico == 0) {
              estado = '<a href="#" title="Sin autorizar"><i class="fa-solid fa-circle" style="color:yellow;width:16px;"></i></a>';
            } else if (row.doc_electronico == 1) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            } else if (row.doc_electronico == 2) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            } else if (row.doc_electronico == 3) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            } else if (row.doc_electronico == 4) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            } else if (row.doc_electronico == 5) {
              estado = '<a href="#" title="Autorizado"><i class="fa-solid fa-circle" style="color:green;width:16px;"></i></a>';
            } else if (row.doc_electronico == 6) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            } else if (row.doc_electronico == 7) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            } else if (row.doc_electronico == 8) {
              estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
            }
            return estado;
          },
        },
        {
          data: "tipo",
        },
        {
          className: 'text-center',
          data: "fecha",
        },
        {
          data: "nombres_paciente",
        },
        {
          data: "seguro",
        },
        {
          data: "procedimientos",
        },
        {
          className: 'text-center',
          data: "fecha_procedimiento",
          render: function (data, type, row) {
            var fecha = row.fecha_procedimiento !== null ? getfecha(row.fecha_procedimiento, null, '-') : '';
            return fecha;
          }
        },
        {
          className: 'text-center',
          data: "estado",
          render: function (data, type, row) {
            var estado =
              row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "usuariocrea",
        },
        {
          className: "text-center",
          data: "usuariomod",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            var descargas = "";




            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            if (row.doc_electronico == 5) {
              descargas = '<a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-code" style="color:green;"></i> Descargar XML</a><a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-pdf" style="color:red;"></i> Descargar PDF</a>';
            }
            var botones = '<div class="btn-group">' +
              '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown">' +
              '<i class="fas fa-list-ol"></i></button>' +
              '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown">' +
              '<span class="sr-only">Toggle Dropdown</span></button>' +
              '<div class="dropdown-menu" role="menu">' +
              '<a class="dropdown-item" href="javascript:;" onclick="verFactura(\'' + row.id + '\', \'' + row.nro_comprobante + '\');">' +
              '<i class="fas fa-eye" style="color:green;"></i> Ver</a>' +
              '<a target="_blank" href="' + $('#base_url').val() + '/v3/' + $('#controlador').val() + '/descargarComprobanteVenta?id=' + row.id + '" class="dropdown-item">' +
              '<i class="fas fa-file-pdf" style="color:black;"></i> Comprobante de venta</a>' +
              '</div></div>';

            return botones;
          },
        },
        {
          data: "nro_autorizacion",
          visible: false
        },
        {
          data: "id_cliente",
          visible: false
        },
        {
          data: "concepto",
          visible: false
        },
        {
          data: "subtotal_12",
          visible: false
        },
        {
          data: "subtotal_0",
          visible: false
        },
        {
          data: "subtotal_total",
          visible: false,
        },
        {
          data: "impuesto",
          visible: false,
        },
        {
          data: "total_final",
          visible: false,
        },
      ],

    });
    $("#tableFacturaCaja").DataTable({
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
      paginate: false,
      info: false,
      responsive: false,
      lengthChange: true,
      autoWidth: true,
      order: [[0, "desc"]],
      iDisplayLength: 1000,
      dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
      buttons: [
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
        url: "v3/" + $("#controlador").val() + "/facturar/" + $("#extra_").val() + "js",
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
          data: "n_comprobante",
          render: function (data, type, row) {

            var botones = '<div class="btn-group"><button type="button" class="btn btn-' +
              $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
              $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="pdfFacturaCaja(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fa fa-upload" style="color:blue"></i></i>Descargar</a><a class="dropdown-item" href="javascript:;" onclick="eliminarFacturaCaja(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-trash-alt" style="color:#00A65A"></i></i> Eliminar</a><a class="dropdown-item" href="javascript:;" onclick="EditarFacturaCaja(\'' + row.id + '\');"><i class="fas fa-edit" style="color:#F39C12"></i></i>Editar</a>' + "</div></div></div>";
            return botones;
          },
        },
        {
          data: "fechaCita",
        },
        {
          className: "text-center",
          data: "horaCita",
        },
        {
          data: "nombres",
        },
        {
          className: "text-center",
          data: "cortesia",
        },
        {
          data: "admision",
        },
        {
          data: "doctor",
        },
        {
          data: "procedimiento",
        },
        {
          className: "text-center",
          data: "seguro",
        },
        {
          className: "text-center",
          data: "id",
        },
        {
          className: "text-center",
          data: "nombre_tipo",
        },
        {
          className: "text-right",
          data: "efectivo",

        },
        {
          className: "text-right",
          data: "tarjeta",
        },

        {
          className: "text-right",
          data: "deposito",
        },
        {
          className: "text-right",
          data: "cheque",

        },
        {
          className: "text-right",
          data: "datalink",
        },
        {
          className: "text-right",
          data: "pagoapp",
        },
        {
          className: "text-right",
          data: "valor_oda",
        },
        {
          className: "text-right",
          data: "total_suma",
        },
        {
          className: "text-right",
          data: "total_suma",
        },
        {
          className: "text-center",
          data: "",
          render: function (data, type, row) {

            var botones = '<td>' +
              '<a class="btn btn-primary btn-xs" onclick="EditarFacturaCaja(\'' + row.id + '\');">' +
              '<i class="fas fa-edit" aria-hidden="true"></i></a>' +
              '<br>' +
              '<a class="btn btn-success btn-xs" onclick="EditarFacturaCaja(\'' + row.id + '\');">' +
              '<i class="fas fa-edit" aria-hidden="true"></i></a>' +
              '</td>';

            $('#Efectivo').text(row.total_global_efectivo);
            $('#Tarjeta').text(row.total_global_tarjeta);
            $('#deposito').text(row.total_global_deposito);
            $('#cheque').text(row.total_global_cheque);
            $('#datalink').text(row.total_global_datalink);
            $('#pago_app').text(row.total_global_app);
            $('#seguro').text(row.total_valor_oda);
            $('#total_vta').text(row.Total_vta);
            $('#honor_medicos').text(row.Total_vta);

            return botones;
          }
        },

      ],
    });

    $("#tableVentasConglomerada").DataTable({
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
      order: [[5, "desc"]],
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
          "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "/conglomeradajs",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
      },
      columns: [
        {
          data: "1",
        },
        {
          data: "nro_comprobante",
        },
        {
          data: "tipo",
        },
        {
          data: "fecha",
        },
        {
          data: "nombreCliente",
        },
        {
          data: "nombres_paciente",
        },
        {
          data: "nombreSeguro",
        },
        {
          data: "procedimientos",
        },
        {
          className: "text-center",
          data: "fecha_procedimiento",
          render: function (data, type, row) {
            if (data == '0000-00-00 00:00:00') {
              return '';
            } else {
              return data;
            }
          },
        },
        {
          data: "nombreCrea",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            if (row.estado == 0) {
              return row.nombreAnula;
            } else {
              return "";
            }
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {

            if (row.electronica == 0) {
              return "No";
            } else if (row.estado_electronica == 3) {
              return "Autorizado";
            } else {
              return "";
            }
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
            var descargas = "";
            if (row.estado == 1) {
              accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
            }
            if (row.doc_electronico == 5) {
              descargas = '<a href="javascvript:;" class="dropdown-item"><i class="fas fa-file-code" style="color:red;"></i> Descargar XML</a><a href="javascvript:;" class="dropdown-item"><i class="fas fa-file-pdf" style="color:red;"></i> Descargar PDF</a>';
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verFacturaConglomerada(' + row.id + ');"><i class="fas fa-eye" style="color:green;"></i> Ver</a>' + descargas + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="anularConglomerada(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
  } catch (error) {
    console.log(error);
  }
  $("#id_forma_pago").select2({
    placeholder: "--Seleccione--",
  });
  $("#id_cuenta_venta_cabecera").select2({
    placeholder: "--Seleccione--",
  });
  $("#agregarLinea").on("click", function () {
    if ($("#id_cliente").val() == "") {
      return mensajes("warning", "Seleccione un cliente para continuar...");
    } else {

      let validacionKardex = $("#kardex").is(':checked');
      var html = '<tr id="tr_' + fila + '">';
      html += '<td><input type="hidden" class="form-control" id="baseImponible_' + fila + '" name="baseImponible[]" readonly /><input type="hidden" class="form-control" id="iva_fila_sin_' + fila + '" name="iva_fila_sin[]" readonly /><input type="hidden" class="form-control" id="iva_fila_' + fila + '" name="iva_fila[]" readonly /><input type="text" class="form-control" id="codigo_' + fila + '" name="codigo[]" readonly /></td>';
      html += '<td style="max-width: 100%;"><select class="form-control select2_producto_fv" id="id_producto_' + fila + '" onchange="asignarProducto(\'' + fila + '\')" name="producto[]"><option value="">--Seleccione--</option></select><br/><textarea id="descripcion_' + fila + '" name="descripcion[]" class="form-control"></textarea></td>';
      html += '<td><input type="text" class="form-control text-right" id="cantidad_' + fila + '" name="cantidad[]" value="1" onblur="calcular();' + (validacionKardex ? 'validarExistencia(' + fila + ');' : '') + '" /><br/><select onchange="' + (validacionKardex ? 'validarExistencia(' + fila + ');' : '') + '" class="form-control select2_bodegas bodega" style="width:100%;" id="bodega_' + fila + '" name="bodega[]"><option value="">--Seleccionar--</option></select></td>';
      html += '<td><input type="text" class="form-control text-right" id="precio_' + fila + '" name="precio[]" value="0.00" onblur="calcular();" /></td>';
      html += '<td><input type="text" class="form-control text-right" id="descuento_' + fila + '" name="descuento[]" value="0.00" onblur="calcular();" /></td>';
      html += '<td><input type="text" class="form-control text-right" id="total_' + fila + '" name="total[]" value="0.00" onblur="calcular();" /></td>';
      html += '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(\'' + fila + "')\"></i></td>";
      html += "</tr>";
      $("#detalleFactura").before(html);
      $(".select2_bodegas").select2({
        width: '100%'
      });
      $(".select2_producto_fv").select2({
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
          url: "v3/" + $("#controlador").val() + "/buscarProducto",
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
      $(".bodega").select2({
        placeholder: "Seleccione una bodega",
        data: [{
          text: "Seleccione",
          id: ""
        }].concat($.map(JSON.parse($('#arrayBodegas').val()), function (method) {
          return {
            text: method.nombre,
            id: method.id,
          }
        }))
      });
      fila++;
      calcular();
    }
  });
  $("#btnAgregarMetodoPago").click(function () {
    var bancosCombo = "";
    var tipoPagoCombo = "";
    var tipoTarjetaCombo = "";
    var now = new Date();
    var day = ("0" + now.getDate()).slice(-2);
    var month = ("0" + (now.getMonth() + 1)).slice(-2);
    var today = now.getFullYear() + "-" + month + "-" + day;
    $.ajax({
      url: "v3/" + $("#controlador").val() + "/getMetodoPagoFactura",
      type: "post",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      dataType: "json",
      success: function (json) {
        $.each(json.tipoPago, function (i, item) {
          tipoPagoCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
        });
        $.each(json.bancos, function (i, item) {
          bancosCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
        });
        $.each(json.tipoTarjeta, function (i, item) {
          tipoTarjetaCombo += '<option value="' + item.id + '">' + item.nombre + "</option>";
        });
        var html_ = '<tr id="trPago_' + filaPago + '">';
        html_ += '<td><select class="form-control select2_" style="width:100%;" id="id_pago_' + filaPago + '" onchange="asignarpago(\'' + filaPago + '\')" name="id_pago[]"><option value="">--Seleccione--</option>' + tipoPagoCombo + "</select></td>";
        html_ += '<td><input type="date" class="form-control" id="fecha_pago_' + filaPago + '" name="fecha_pago[]" value="' + today + '" /></td>';
        html_ += '<td><select class="form-control select2_" style="width:100%;" id="id_tarjetas_' + filaPago + '" name="id_tarjetas[]"><option value="">--Seleccione--</option>' + tipoTarjetaCombo + "</select></td>";
        html_ += '<td><input type="text" class="form-control text-right" id="numero_' + filaPago + '" name="numero[]" value="" /></td>';
        html_ += '<td><select class="form-control select2_" style="width:100%;" id="id_bancos_' + filaPago + '" name="id_bancos[]"><option value="">--Seleccione--</option>' + bancosCombo + "</select></td>";
        html_ += '<td><input type="text" class="form-control text-right" id="cuenta_' + filaPago + '" name="cuenta[]" value="" /></td>';
        html_ += '<td><input type="text" class="form-control" id="giradoA_' + filaPago + '" name="giradoA[]" value="" /></td>';
        html_ += '<td><input type="text" class="form-control text-right" id="valor_' + filaPago + '" name="valor[]" value="" /></td>';
        html_ += '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="PagoEliminar(\'' + filaPago + "')\"></i></td>";
        html_ += "</tr>";
        $("#metodoPago").append(html_);
        $(".select2_").select2();
        filaPago++;
      },
    });
  });
  $("#usuario_vendedor").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarRecaudador",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term, tipo: 17 };
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
  $("#recaudador").select2({
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
      url: "v3/" + $("#controlador").val() + "/buscarRecaudador",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term, tipo: 18 };
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
  $("#id_cliente").select2({
    allowClear: true,
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
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
    allowClear: true,
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarPaciente",
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

  $("#id_paciente").select2({
    allowClear: true,
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 3,
    ajax: {

      url: "v3/" + $("#controlador").val() + "/buscarPaciente",
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
  $("#id_doctor").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarDoctores",
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
  $("#id_cliente").change(function () {
    var datos = $(this).val().split("|");
    $("#identificacion_cliente").val(datos[0]);
    var telefono1 = datos[1] != "" ? datos[1] : "";
    var telefono2 = datos[2] != "" ? datos[2] : "";
    var telefono = "";
    if (telefono2 != "" && telefono1 != "") {
      telefono = telefono1 + " - " + telefono2;
    } else if (telefono2 == "" && telefono1 != "") {
      telefono = telefono1;
    } else if (telefono2 != "" && telefono1 == "") {
      telefono = telefono2;
    }
    $("#telefono_cliente").val(telefono);
    $("#direccion_cliente").val(datos[3]);
    $("#email_cliente").val(datos[4]);
  });
  $("#id_paciente").change(function () {
    var datos = $(this).val().split("|");
    $("#identificacion_cliente").val(datos[0]);
    var telefono1 = datos[1] != "" ? datos[1] : "";
    var telefono2 = datos[2] != "" ? datos[2] : "";
    var telefono = "";
    if (telefono2 != "" && telefono1 != "") {
      telefono = telefono1 + " - " + telefono2;
    } else if (telefono2 == "" && telefono1 != "") {
      telefono = telefono1;
    } else if (telefono2 != "" && telefono1 == "") {
      telefono = telefono2;
    }
    $("#telefono_paciente").val(telefono);
    $("#direccion_paciente").val(datos[3]);
    $("#email_paciente").val(datos[4]);
  });
  $("#id_seguro").change(function () {
    $.ajax({
      url: "v3/" + $("#controlador").val() + "/buscarSeguro",
      type: "get",
      data: {
        id: $(this).val(),
      },
      dataType: "json",
      success: function (json) {
        $("#nivel").empty();
        $("#nivel").append('<option value="">--Seleccione--</option>');
        if (json != "") {
          $.each(json, function (i, item) {
            $("#nivel").append(
              "<option value='" + item.id + "'>" + item.nombre + "</option>"
            );
          });
        }
      },
    });
  });
  $("#btnagregarAdicional").on("click", function () {
    let contador = Number($("#contadorAdicionales").val());
    if (contador <= 4) {
      let structuraHtmlAdicionales = `
                <tr id='detalle_${contador}'>
                    <td><input type='text' class='form-control' id='nombreAdicional_${contador}' name='nombreAdicional[]' placeholder="Nombre" /></td>
                    <td><input type='text' class='form-control' id='valorAdicional_${contador}' name='valorAdicional[]' placeholder="Detalle" /></td>
                    <td><a href="javascript:;" class="btn btn-danger" onclick="eliminarDetalle(${contador});"><i class="fa fa-trash-alt"></i></a></td>
                </tr>
        `;
      let idDespuesdeIncrementar = contador + 1;
      $("#contadorAdicionales").val(idDespuesdeIncrementar);
      $("#tablaAdicionales").append(structuraHtmlAdicionales);
    } else {
      return mensajes("warning", "No puede agregar más de 5 filas");
    }
  });
  $("#btnguardarFactura").click(function () {
    if (fila <= 0) {
      return mensajes("warning", "Agregue productos para continuar...");
    } else if ($("#fecha_emision").val() == "") {
      return mensajes("warning", "Seleccione la fecha de emisión para continuar...");
    } else if ($("#establecimiento").val() == "") {
      return mensajes("warning", "Seleccione el establecimiento para continuar...");
    } else if ($("#emision").val() == "") {
      return mensajes("warning", "Seleccione la emisión para continuar...");
    } else if ($("#id_cliente").val() == "") {
      return mensajes("warning", "Seleccione un cliente para continuar...");
    } else if ($("#concepto").val() == "") {
      return mensajes("warning", "Debe ingresar el concepto de la factura para continuar...");
    } else {
      if (fila > 0) {
        let validacionProducto = false;
        let validacionCantidad = false;
        let validacioMetodo = false;
        let validacionBodega = false;
        $("select[name='producto[]']").each((i, v) => {
          if (v.value == "") {
            validacionProducto = true;
          }
          if ($("#cantidad_" + i).val() === "") {
            validacionCantidad = true;
          }
          if ($("#id_pago_" + i).val() == '') {
            validacioMetodo = true;
          }
          if ($("#kardex").is(':checked')) {
            if ($("#bodega_" + i).val() == "") {
              validacionBodega = true;
            }
          }
        });
        if (validacioMetodo) {
          return mensajes("warning", "Debe ingresar un metodo de pago..."
          );
        }
        if (validacionProducto) {
          return mensajes("warning", "Debe ingresar un producto para continuar..."
          );
        }
        if (validacionCantidad) {
          return mensajes("warning", "Debe ingresar la cantidad del producto para continuar...");
        }
        if (validacionBodega) {
          return mensajes("warning", "Debe ingresar la bodega para poder continuar..");
        }
        if ($("#id_pago_0").val() == 2) {
          let numeroVacio = false;
          let bancoVacio = false;
          $("input[name='numero[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              numeroVacio = true;
            }
          });
          $("select[name='id_bancos[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              bancoVacio = true;
            }
          });
          if (numeroVacio) {
            return mensajes("warning", "Debe ingresar el número de cheque para continuar...");
          }
          if (bancoVacio) {
            return mensajes("warning", "Debe seleccionar un banco para continuar...");
          }
        }
        if ($("#id_pago_0").val() == 3) {
          let numeroVacio = false;
          let bancoVacio = false;
          $("input[name='numero[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              numeroVacio = true;
            }
          });
          $("select[name='id_bancos[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              bancoVacio = true;
            }
          });
          if (numeroVacio) {
            return mensajes("warning", "Debe ingresar el número de Deposito para continuar...");
          }
          if (bancoVacio) {
            return mensajes("warning", "Debe seleccionar un banco para continuar...");
          }
        }
        if ($("#id_pago_0").val() == 4) {
          let bancoVacio = false;
          let tarjeta = false;
          $("select[name='id_tarjetas[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              tarjeta = true;
            }
          });

          $("select[name='id_bancos[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              bancoVacio = true;
            }
          });
          if (tarjeta) {
            return mensajes("warning", "Debe seleccionar una tarjeta para continuar...");
          }
          if (bancoVacio) {
            return mensajes("warning", "Debe seleccionar un banco para continuar...");
          }
        }
        if ($("#id_pago_0").val() == 5) {
          let numeroVacio = false;
          let bancoVacio = false;
          $("input[name='numero[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              numeroVacio = true;
            }
          });
          $("select[name='id_bancos[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              bancoVacio = true;
            }
          });
          if (numeroVacio) {
            return mensajes("warning", "Debe ingresar el número de transferencia para continuar...");
          }
          if (bancoVacio) {
            return mensajes("warning", "Debe seleccionar un banco para continuar...");
          }
        }
        if ($("#id_pago_0").val() == 6) {
          let bancoVacio = false;
          let tarjeta = false;
          $("select[name='id_tarjetas[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              tarjeta = true;
            }
          });
          $("select[name='id_bancos[]']").each((i, v) => {
            if ($(v).val().trim() === "") {
              bancoVacio = true;
            }
          });
          if (tarjeta) {
            return mensajes("warning", "Debe seleccionar una tarjeta para continuar...");
          }
          if (bancoVacio) {
            return mensajes("warning", "Debe seleccionar un banco para continuar...");
          }
        }
      }
      if (filaPago <= 0) {
        return mensajes("warning", "Debe tener por lo menos una forma de pago registrada...", "");
      } else {
        let validFormPago = false;
        let total = $("#total_pagar").val();
        let tot = 0;
        $("input[name='valor[]']").each((i, v) => {
          if (v.value == "") {
            validFormPago = true;
          }
          tot += Number(v.value);
        });
        if (validFormPago) {
          return mensajes("warning", "Tiene algun campo valor en la forma de pago vacio..", "");
        }
        if (total !== parseFloat(tot).toFixed(2)) {
          return mensajes("warning", "El valor total y el valor de la forma de pago no coinciden..", "");
        }
      }

      var json = ajax($('#action').val(), $('#frm_factura').serialize(), 'json');
      if (json.responseJSON !== null) {
        json = json.responseJSON;
        if (json.status == 'success') {
          Swal.fire({
            title: json.message,
            text: $('#informa').val(),
            icon: json.status,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok',
          }).then((result) => {
            if (result.isConfirmed) {
              window.open($('#base_url').val() + '/v3/ventas/listadeventas', '_self');
            }
          });
        } else {
          Swal.fire({
            title: json.message,
            text: $('#informa').val(),
            icon: json.status,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok',
          })
        }
      }
      //mensajes('question', $("#pregunta").val(), 'frm_factura', 'action', '', $('#base_url').val() + 'v3/ventas/listadeventas', '');
    }
  });
  $("input[data-bootstrap-switch]").each(function () {
    $(this).bootstrapSwitch("state", $(this).prop("checked"));
  });
  $("#js-data-cedula").select2({
    minimumInputLength: 2,
    cache: true,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarCliente",
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
              text: item.nombreappe,
              id: item.id,
              tipo: item.opcion,
            };
          }),
        };
      },
    },
    language: {
      inputTooShort: function () {
        return "Por favor ingrese 2 caracteres....";
      },
    },
  })
  $('#btnGuardarCliente').click(function () {
    if ($('#tipo_identificacion').val() == '') {
      return mensajes('warning', 'Debe seleccionar el tipo de identificación...');
    } else if ($('#nombre').val() == '') {
      return mensajes('warning', 'Debe ingresar el nombre del cliente...');
    } else if ($('#identificacion').val() == '') {
      return mensajes('warning', 'Debe ingresar el # de identificación del cliente...');
    } else if ($('#clase').val() == '') {
      return mensajes('warning', 'Debe seleccionar la clase del cliente...');
    } else if ($('#pais').val() == '') {
      return mensajes('warning', 'Seleccione e país del cliente...');
    } else if ($('#tipo_identificacion').val() == 5 && $('#identificacion').val().length != 10) {
      return mensajes('info', 'el número de la identificación debe contener 10 dígitos');
    } else if ($('#tipo_identificacion').val() == 4 && $('#identificacion').val().length != 13) {
      return mensajes('info', 'el número de la identificación debe contener 13 dígitos');
    } else {
      mensajes('question', $('#pregunta').val(), 'frm_', 'action_cliente');
    }
  });
  $('#btnSearchModalRenta').click(function () {
    if ($('#total').val() == 0.00) {
      mensajes('warning', 'Para agregar una retención debe tener valor a facturar');
    } else {
      var json = ajax('v3/' + $('#controlador').val() + '/buscarRenta', null, 'html');
      if (json.responseText !== null) {
        $('#cuerpoRenta').html(json.responseText);
        $('#searchModalIr').modal('toggle');
      }
    }
  });
  $('#btnSearchModalIva').click(function () {
    if ($('#iva15').val() == 0.00 || $('#iva15').val() == 0.00) {
      mensajes('warning', 'Para agregar una retención debe tener valor a facturar');
    } else {
      var json = ajax('v3/' + $('#controlador').val() + '/buscarFuenteIva', null, 'html');
      if (json.responseText !== null) {
        $('#cuerpoIva').html(json.responseText);
        $('#searchModalIva').modal('toggle');
      }
    }
  });
});
function ubicarRetencion(codigo) {
  $('#retir' + $('#contador').val()).val(codigo);
  var base_ = parseFloat($('#subtotal_12').val()) + parseFloat($('#subtotal_5').val()) + parseFloat($('#subtotal_0').val());
  var data = {
    codigo: codigo,
    tipo: 2,
    base: base_
  };
  var json = ajax('v3/' + $('#controlador').val() + '/getRenta', data, 'html');
  if (json.responseText !== null) {
    $('#cuerpoRetenciones').html(json.responseText);
  }
  $('#searchModalIr').modal('toggle');
}
function buscarCodigoRetencion() {
  var input, filter, table, tr, td, i, txtValue;
  input = document.getElementById("txtBuscar");
  filter = input.value.toUpperCase();
  table = document.getElementById("tablaDatos");
  tr = table.getElementsByTagName("tr");
  for (i = 1; i < tr.length; i++) { // Comenzar desde 1 para saltar el encabezado
    td = tr[i].getElementsByTagName("td")[1]; // Busca en la columna de descripción
    if (td) {
      txtValue = td.textContent || td.innerText;
      if (txtValue.toUpperCase().indexOf(filter) > -1) {
        tr[i].style.display = "";
      } else {
        tr[i].style.display = "none";
      }
    }
  }
}
function validarCampo(selector, mensaje) {
  let campoVacio = false;
  $(selector).each((i, v) => {
    if ($(v).val().trim() === "") {
      campoVacio = true;
    }
  });
  if (campoVacio) {
    return mensajes("warning", mensaje);
  }
  return false;
}
function asignarProducto(id) {
  var datos = $("#id_producto_" + id).val().split("|");
  $("#codigo_" + id).val(datos[1]);
  $("#cantidad_" + id).focus();
  traerPrecio(datos[1]);
  calcular();
}
function traerPrecio(id) {
  var data = {
    id: id,
    id_seguro: $('#id_seguro').val(),
    id_nivel: $('#nivel').val(),
  }
  var json = ajax('v3/' + $('#controlador').val() + '/traerPrecio', data, 'json');
}
function ocultarBoton() {
  let valor = $("#pagado").is(":checked");
  // !false
  if (!valor) {
    $("#divCuentas").hide();
  } else {
    $("#divCuentas").show();
  }
}
function eliminarDetalle(id) {
  let contador = Number($("#contadorAdicionales").val());
  $("#detalle_" + id).remove();
  let idDespuesdeIncrementar = contador - 1;
  $("#contadorAdicionales").val(idDespuesdeIncrementar);
}
function agregarVenta(id) {
  window.open("v3/" + $("#controlador").val() + "/facturar/facturaventa", "_self");
}
function verFactura(id, num) {
  $("#titulo_modal").html('<i class="fas fa-edit"></i> No.: ' + num);
  var data = { id: id };
  var html = ajax("v3/" + $("#controlador").val() + "/getVentaPrint", data, "html")
  html = html.responseText;
  $("#contenidoHtml").html(html);
  $("#myModal").modal("toggle");
}
function eliminarFila(num) {
  $("#tr_" + num).remove();
  fila--;
  calcular();
}
function PagoEliminar(filaPago) {
  $("#trPago_" + filaPago).remove();
  filaPago--;
}
function calcular() {
  var descuento = 0;
  var subtotal0 = 0;
  var subtotal12 = 0;
  var iva = 0;
  let baseImponibleTotal = 0;
  let baseImponible = 0;
  let ivasin = 0;
  var total = 0;
  let sumaivasin = 0;
  var total_pagar = 0;
  try {
    let cantidad = 0;
    let precio = 0;
    let precioUni = 0;
    $("input[name='cantidad[]']").each((i, v) => {
      cantidad += Number(v.value);
    });
    $("input[name='precio[]']").each((i, v) => {
      let is_iva = [];
      precioUni = Number(v.value) * Number($("#cantidad_" + i).val()) - Number($("#descuento_" + i).val());


      is_iva = $("#id_producto_" + i).val() ? $("#id_producto_" + i).val().split("|") : [];

      descuento += Number($("#descuento_" + i).val());

      if (is_iva.length > 0 && is_iva[3] > 0) {

        ivasin = Number(v.value) * Number($("#cantidad_" + i).val());
        sumaivasin += ivasin;
        subtotal12 += precioUni;
        $("#iva_fila_" + i).val(parseFloat(precioUni) * parseFloat($('#impuestoIva').val()));
        $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * parseFloat($('#impuestoIva').val()));
        $("#baseImponible_" + i).val(Number(v.value) * Number($("#cantidad_" + i).val()));
      } else {
        subtotal0 += precioUni;
        $("#iva_fila_" + i).val("");
        $("#iva_fila_sin_" + i).val("");
        $("#baseImponible_" + i).val("");
      }
      baseImponible += Number(v.value) * Number($("#cantidad_" + i).val());
      $("#total_" + i).val(precioUni);
      precio += Number(v.value);
    });
  } catch (error) {
    console.log(error.message);
  }
  if (isNaN(subtotal0)) {
    subtotal0 = 0;
  }

  if (isNaN(descuento)) {
    descuento = 0;
  }

  total = subtotal0 + subtotal12;
  subtotal0 = parseFloat(subtotal0);
  subtotal12 = parseFloat(subtotal12);
  iva = parseFloat(subtotal12) * parseFloat($('#impuestoIva').val());
  baseImponibleTotal = parseFloat(baseImponible) + parseFloat(sumaivasin * parseFloat($('#impuestoIva').val()));
  total_pagar = parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(iva) + parseFloat($("#propina").val());
  $("#subtotal12").val(subtotal12.toFixed(2));
  $("#subtotal0").val(subtotal0.toFixed(2));
  $("#descuentototal").val(descuento.toFixed(2));
  $("#baseImponibleTotal").val(baseImponibleTotal.toFixed(2));
  $("#subtotal").val(total.toFixed(2));
  $("#iva").val(iva.toFixed(2));
  $("#total_pagar").val(total_pagar.toFixed(2));
}
function asignarpago(id) {
  let valor = $("#id_pago_" + id).val();
  if (valor == 1) {//Efectivo

    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'table-column');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);

  }
  else if (valor == 2) {//Cheque

    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", false);

  }
  else if (valor == 3) {//Deposito
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if (valor == 4) {//Tarjeta de credito
    $("#id_tarjetas_" + id).parent().css("display", 'block');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if (valor == 5) {//Transferencia
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if (valor == 6) {//Tarjeta de debito
    $("#id_tarjetas_" + id).parent().css("display", 'block');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if (valor == 7) {//Pendiente de pago
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'table-column');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  } else {
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'table-column');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  }
}
$("#agregarCliente").on("click", function () {
  limpiarModal();
  $("#codigo").removeAttr("readonly");
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $('#viene').val('factura');
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i> ' + $("#nuevo").val() + " " + $("#submodulo_").val());
  $("#myModal").modal("toggle");
  /*
  $.ajax({
    url: 'v3/' + $('#controlador').val() + '/modalCliente',
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    type: "GET",
    dataType: "html",
    success: function (data) {
      $("#bodyhtml").html(data);
      $("#modal_clientes").modal('show');
    },
    error: function (xhr, status) {
      alert("Existió un problema");
    },
  });
  */
});
function agregarConglomerada() {
  window.open("v3/" + $("#controlador").val() + "/facturar/crearFacturaConglomerada", "_self");
}
$("#sucursal_empre").on("change", function () {
  let sucursal = $(this).val();
  $.ajax({
    type: "post",
    url: "v3/guiaderemision/getPuntoEmision",
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
$('#btn_pago').click(function (event) {
  id = document.getElementById('contador_pago').value;
  var midiv_pago = document.createElement("tr")
  midiv_pago.setAttribute("id", "dato_pago" + id);
  let fecha = new Date().toISOString().split('T')[0];
  midiv_pago.innerHTML = `
        <td>
          <select class="form-control pago selectMetodoPago" name="id_pago[]" id="id_pago_${id}" style="width: 100%;" onchange="revisar_componentes(this,${id});">
        </td>
        <td>
          <input type="date" class="form-control input-number" value="${fecha}" name="fecha_pago[]" id="fecha_pago_${id}">
        </td>
        <td>
          <select class="form control selectTarjeta"  id="id_tarjetas_${id}" name="id_tarjetas[]" style="width: 100%;"></select>
        </td>
        <td>
          <input type="text" class="form-control" name="numero[]" id="numero_${id}">
        </td>
        <td>
          <select class="form-control selectBanco" name="id_bancos[]" id="id_bancos_${id}"></select>
          </td>
          <td>
            <input  class="form-control" name="cuenta[]" id="cuenta_${id}" >
          </td>
          <td>
            <input class="form-control" type="text" id="giradoA_${id}" name="giradoA[]">
          </td>
          <td>
            <input class="form-control text-right input-number fpago" type="text" id="valor_${id}" name="valor[]" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(${id})" onkeypress="return soloNumeros(this);">
          </td>
          <td>
            <button style="text-align:center;" type="button" class="btn btn-danger btn-gray delete btn-xs">
            <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
          </td>`;

  document.getElementById('agregar_pago').appendChild(midiv_pago);
  id = parseInt(id);
  id = id + 1;
  document.getElementById('contador_pago').value = id;


  $(".selectMetodoPago").select2({
    placeholder: "Seleccione un método de pago",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayMetodoPago').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      };
    }))
  });


  $(".selectTarjeta").select2({
    width: "100%",
    placeholder: "Tipo Tarjeta",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayMetodoTarjeta').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      }
    }))
  });



  $(".selectBanco").select2({
    width: "100%",
    placeholder: "Seleccione un banco",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayMetodoBanco').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      }
    }))
  });
});
function crearInfoTributaria() {
  window.open("v3/" + $("#controlador").val() + "/crearInfoTributaria?s=" + $('#emision option:selected').html() + '&e=' + $('#establecimiento').val() + '&p=' + $('#emision').val(), "new");
}
function validarExistencia(fila) {
  let validacionKardex = $("#kardex").is(':checked');
  if (validacionKardex) {
    let id_producto = $('#id_producto_' + fila).val();
    let cantidad = $('#cantidad_' + fila).val();
    let bodega = $('#bodega_' + fila).val();

    let data = {
      id_producto, cantidad, bodega
    };
    if (id_producto !== "" && cantidad !== "" && bodega !== "") {
      var json = ajax("v3/" + $("#controlador").val() + "/verificarStock", data, 'json');
      if (json.responseJSON != '') {
        json = json.responseJSON;
        if (json.status == 'error') {
          return mensajes("warning", `${json.msj.toUpperCase()}`);
        } else {
          return mensajes("warning", `${json.msj.toUpperCase()}`);
        }
      }
    }
  }

}
$('body').on('click', '.relactivo', function () {
  if ($(this).prop("checked") == true) {
    $(this).parent().find('.veractivo').val(1);
    $(this).parent().find('.patients').attr('name', 'id_orden[]');
    $(this).parent().find('.desc_producto').attr('name', 'descrip_prod[]');
    $(this).parent().find('.table').removeClass('noacti');
    totales(0);
  } else if ($(this).prop("checked") == false) {
    $(this).parent().find('.veractivo').val(0);
    $(this).parent().find('.patients').attr('name', '');
    $(this).parent().find('.desc_producto').attr('name', '');
    totales(0);

  }
});
function totales(e) {
  var subt12 = [];
  var subt0 = [];
  var descuento = [];
  var sb12 = 0;
  var sb0 = 0;
  var descuentosub0 = 0;
  var descuentosub12 = 0;
  var d = 0;
  var copagoTotal = 0;
  if (e == 0) {
    $('.cneto').each(function (i, obj) {
      var veractivo = $(this).parent().parent().parent().parent().hasClass('noacti');
      if (veractivo == false) {
        cnx(this);
        var cant = $(this).val();
        $(this).parent().prev().find('.productos').attr('name', 'codigo[]');
        $(this).parent().prev().find('.codigo_producto').attr('name', 'nombre[]');
        $(this).parent().prev().find('.id_patient').attr('name', 'paciente[]');

        $(this).attr('name', 'cantidad[]');
        var precio = $(this).parent().next().children().eq(0).val();
        $(this).parent().next().children().attr('name', 'precio[]');
        var copago = $(this).parent().next().next().children().val();
        $(this).parent().next().next().children().attr('name', 'copago[]');
        var descuento = $(this).parent().next().next().next().next().children().val();
        $(this).parent().next().next().next().next().children().attr('name', 'desc[]');
        $(this).parent().next().next().next().next().next().children().attr('name', 'totalFinal[]');
        d = parseFloat(d) + parseFloat(descuento);
        var iva = $(this).parent().next().next().next().next().next().next().children().prop('checked');
        precio = precio != null ? precio : 0;

        var total = (parseInt(cant) * parseFloat(precio)) - parseFloat(0) - parseFloat(copago);
        total = redondeafinal(total, 2);
        if (iva == 1) {
          subt12.push(total);
          sb12 = sb12 + total;
          descuentosub12 += parseFloat(descuento);

        } else {
          subt0.push(total);
          sb0 = sb0 + total;
          descuentosub0 += parseFloat(descuento);
        }
        copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
        $("#subtotal_12").html(sb12.toFixed(2));
        $("#subtotal_0").html(sb0.toFixed(2));
        $("#descuento").html(d.toFixed(2));
        var descuento_total = descuentosub12 + descuentosub0;
        var sum = sb12 + sb0 - descuento_total;
        $("#base").html(sum.toFixed(2));
        var iva = $("#ivareal").val();

        // Colocar el iva en el campo
        var ti = iva * sb12;

        $(this).parent().next().next().next().next().next().next().children().val(ti);

        if (d > 0) {
          if (sb12 > 0) {
            ti = iva * (sb12 - descuentosub12);
          }

        }
        ti = redondeafinal(ti);
        $("#tarifa_iva").html(ti.toFixed(2, 2));
        var totax = sum + ti;
        totax = redondeafinal(totax);
        copagoTotal = redondeafinal(copagoTotal);
        sb12 = redondeafinal(sb12);
        sb0 = redondeafinal(sb0);
        d = redondeafinal(d);

        $("#total").html(totax.toFixed(2, 2));
        $("#copagoTotal").html(copagoTotal.toFixed(2, 2));
        $("#subtotal_121").val(sb12);
        $("#subtotal_01").val(sb0);
        $("#descuento1").val(d);
        $("#tarifa_iva1").val(ti);
        $("#total1").val(totax);
        $("#totalc").val(copagoTotal);
      } else {
        $(this).parent().prev().find('.codigo_producto').attr('name', '');
        $(this).parent().prev().find('.id_patient').attr('name', '');
        $(this).parent().prev().find('.productos').attr('name', '');
        $(this).attr('name', '');
        $(this).parent().next().children().attr('name', '');
        $(this).parent().next().next().children().attr('name', '');
        $(this).parent().next().next().next().next().children().attr('name', '');
      }
    });

  }
}
function cnx(e) {
  var cant = $(e).val();
  var precio = $(e).parent().next().children().val();
  var copago = $(e).parent().next().next().children().val();
  var descuento = $(e).parent().next().next().next().next().children().val();
  var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
  total = redondeafinal(total);
  $(e).parent().next().next().next().next().next().children().val(total);
}
function redondeafinal(num, decimales = 2) {
  var signo = (num >= 0 ? 1 : -1);
  num = num * signo;
  if (decimales === 0)
    return signo * Math.round(num);
  num = num.toString().split('e');
  num = Math.round(+(num[0] + 'e' + (num[1] ? (+num[1] + decimales) : decimales)));
  num = num.toString().split('e');
  return signo * (num[0] + 'e' + (num[1] ? (+num[1] - decimales) : -decimales));
}
$('body').on('blur', '.pneto', function () {
  var cant = $(this).parent().prev().children().val();
  var copago = $(this).parent().next().children().val();
  var descuento = $(this).parent().next().next().next().children().val();
  var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
  total = redondeafinal(total);
  $(this).parent().next().next().next().next().children().val(total);
  if ($("#modR").val() == 1) {
    totalesRC(0);

  } else {
    totales(0);
  }

});
$('body').on('active', '.pneto', function () {
  var cant = $(this).parent().prev().children().val();
  var copago = $(this).parent().next().children().val();
  var descuento = $(this).parent().next().next().next().children().val();
  var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
  total = redondeafinal(total);
  $(this).parent().next().next().next().next().children().val(total);
  if ($("#modR").val() == 1) {
    totalesRC(0);

  } else {
    totales(0);
  }
});
$('body').on('change', '.pneto', function () {
  var cant = $(this).parent().prev().children().val();
  var copago = $(this).parent().next().children().val();
  var descuento = $(this).parent().next().next().next().children().val();
  var total = (parseInt(cant) * parseFloat($(this).val())) - descuento - copago;
  total = redondeafinal(total);
  $(this).parent().next().next().next().next().children().val(total);
  if ($("#modR").val() == 1) {
    totalesRC(0);

  } else {
    totales(0);
  }
});
$('body').on('change', '.cneto', function () {
  var cant = $(this).val();
  var precio = $(this).parent().next().children().val();
  var copago = $(this).parent().next().next().children().val();
  var descuento = $(this).parent().next().next().next().next().children().val();
  var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
  total = redondeafinal(total);
  $(this).parent().next().next().next().next().next().children().val(total);
  if ($("#modR").val() == 1) {
    totalesRC(0);

  } else {
    totales(0);
  }
});
function revisar_componentes(e, id) {
  if ($(e).val() == 1) {
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'table-column');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if ($(e).val() == 2) {//Cheque
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", false);
  }
  else if ($(e).val() == 3) {//Deposito
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if ($(e).val() == 4) {//Tarjeta de credito
    $("#id_tarjetas_" + id).parent().css("display", 'block');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if ($(e).val() == 5) {//Transferencia
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", false);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", false);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if ($(e).val() == 6) {//Tarjeta de debito
    $("#id_tarjetas_" + id).parent().css("display", 'block');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'block');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  }
  else if ($(e).val() == 7) {//Pendiente de pago
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'table-column');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  } else {
    $("#id_tarjetas_" + id).parent().css("display", 'table-column');
    $("#numero_" + id).prop("readonly", true);
    $("#id_bancos_" + id).parent().css("display", 'table-column');
    $("#cuenta_" + id).prop("readonly", true);
    $("#giradoA_" + id).prop("readonly", true);
  }
}
function buscarOrdenes() {
  if ($("#fecha_desde").val() == "") {
    return mensajes("warning", "Seleccione una fecha desde...");
  } else if ($("#fecha_hasta").val() == "") {
    return mensajes("warning", "Seleccione una fecha hasta...");
  }
  else if ($("#seguro_f").val() == "") {
    return mensajes("warning", "Seleccione un seguro...");
  }
  else if ($("#tiposervicio_f").val() == "") {
    return mensajes("warning", "Seleccione un tipo de servicio...");
  } else {
    $.ajax({
      url: 'v3/' + $('#controlador').val() + '/buscarInsumos',
      headers: {
        "X-CSRF-TOKEN": $("input[name=_token]").val(),
      },
      data: {
        fecha_desde: $("#fecha_desde").val(),
        fecha_hasta: $("#fecha_hasta").val(),
        tiposervicio_f: $("#tiposervicio_f").val(),
        seguro_f: $("#seguro_f").val(),
      },
      type: "GET",
      dataType: "html",
      success: function (data) {
        let elemento = document.getElementById("items");
        elemento.style.height = "400px";

        $("#items").html(data);

      },
      error: function (xhr, status) {
        alert("Existió un problema");
      },
    });
  }
}
$("#guardarConglomerada").on("click", function () {
  let divElement = document.getElementById("agregar_pago");

  if ($("#sucursal_empre").val() == '') {
    return mensajes("warning", "Debe ingresar la sucursal...");
  } else if ($("#divisas").val() == '') {
    return mensajes("warning", "Debe ingresar la divisa...");
  } else if ($("#identificacion").val() == null) {
    return mensajes("warning", "Debe ingresar la identificación...");
  } else if ($("#concepto").val() == '') {
    return mensajes("warning", "Debe ingresar el concepto...");
  } else if ($("#dias_plazo").val() == '') {
    return mensajes("warning", "Debe ingresar los dias plazo...");
  } else if (!(divElement.innerText.trim().length > 0) && !(divElement.innerHTML.trim().length > 0)) {
    return mensajes("warning", "Debe ingresar un metodo de pago...");
  } else if (($("#valor_0").val() == undefined || $("#valor_0").val() == '0') || ($("#id_pago_0").val() == undefined || $("#id_pago_0").val() == '')) {
    for (let index = 0; index < $("#contador_pago").val(); index++) {
      if ($("#valor_" + index).val() == '0') {
        return mensajes("warning", "Debe ingresar un valor de pago...");
      }
      if ($("#id_pago_" + index).val() == '') {
        return mensajes("warning", "Debe ingresar un tipo de pago...");
      }
    }
  } else {
    // mensajes("question", $("#pregunta").val(), "frm_conglomerada", "action");
    mensajes("question", $("#pregunta").val(), "frm_conglomerada", "action", "v3/ventas/facturar/facturaconglomerada");
  }
});
function revision_total(id) {
  var valor = $('#valor' + id).val();
  ntotal = valor * 1;
  $('#valor_base' + id).val(ntotal.toFixed(2));
}
function soloNumeros(e) {
  var teclaPulsada = window.event ? window.event.keyCode : e.which;
  var valor = e.value;
  if (teclaPulsada == 45 && valor.indexOf("-") == -1) {
    document.getElementById("inputNumero").value = "-" + valor;
  }
  if (teclaPulsada == 13 || (teclaPulsada == 46 && valor.indexOf(".") == -1)) {
    return true;
  }
  return /\d/.test(String.fromCharCode(teclaPulsada));
}
$("#numero").on("change", function () {
  ingresar_cero();
});
function verFacturaConglomerada(id) {
  window.open($("#base_url").val() + "/v3/ventas/facturar/crearFacturaConglomerada?id=" + id, "_self");
}
function regresar() {
  history.back();
}
try {
  document.getElementById("dias_plazo").addEventListener("keydown", function (event) {
    if (!/[0-9]/.test(event.key) && event.key !== "Backspace" && event.key !== "Delete" && event.key !== "ArrowLeft" && event.key !== "ArrowRight") {
      event.preventDefault();
    }
  });
} catch (error) {
  console.log(error);
  //window.open('v3/admin');
}
function anularConglomerada(id, estado) {
  Swal.fire({
    title: '¿Desea Anular este factura conglomerada?',
    text: $('#informa').val(),
    icon: 'info',
    showCancelButton: true,
    cancelButtonText: 'No',
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'Si'
  }).then((result) => {
    if (result.isConfirmed) {
      testConglomerada(id, estado);
    }
  });
}
async function testConglomerada(id, estado) {
  var mensaje = "";
  let msj_ingr = "";
  let msj_ret = "";
  let msj_cruce = "";
  let msj_che = "";
  let msj_credito = "";
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
        url: "v3/" + $('#controlador').val() + "/ventasVerificar",
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

          if (typeof (data.ingreso) != "undefined") {
            if (data.ingreso.length > 0) {
              for (let i = 0; i < data.ingreso.length; i++) {
                mensaje = `${data.ingreso[i]}`;
              }
              msj_ingr = ` Ingreso: ${mensaje}`;
              mensaje = "ok";
            }
          }
          if (typeof (data.retencion) != "undefined") {
            msj_ret = ` Retencion: ${data.retencion}`;
            mensaje = "ok";
          }
          if (typeof (data.cruce) != "undefined") {
            for (let i = 0; i < data.cruce.length; i++) {
              mensaje = `${data.cruce[i]}`;
            }
            msj_cruce = ` Cruces: ${mensaje}`;
            mensaje = "ok";
          }
          if (typeof (data.chequepost) != "undefined") {
            for (let i = 0; i < data.chequepost.length; i++) {
              mensaje = `${data.chequepost[i]}`;
            }
            msj_che = ` Chequepost: ${mensaje}`;
            mensaje = "ok";
          }
          if (typeof (data.cruce_cuentas) != "undefined") {

            for (let i = 0; i < data.cruce_cuentas.length; i++) {
              mensaje = `${data.cruce_cuentas[i]}`;
            }
            msj_cruce = ` Cruce cuentas: ${mensaje}`;
            mensaje = "ok";
          }
          if (typeof (data.credito) != "undefined") {

            for (let i = 0; i < data.credito.length; i++) {
              mensaje = `${data.credito[i]}`;
            }
            msj_credito = ` Credito: ${mensaje}`;
            mensaje = "ok";
          }
          if (mensaje != "") {
            Swal.fire("Error!", `Existen algunos observaciones encontradas: ${msj_ingr}${msj_ret}${msj_cruce}${msj_che}${msj_cruce}${msj_credito}`, "error");
          } else {

            anularDefinivo(id, text);
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
function anularDefinivo(id, text) {
  $.ajax({
    url: 'v3/' + $('#controlador').val() + '/anularVenta',
    data: {
      id: id,
      concepto: text
    },
    type: "GET",
    dataType: "json",
    success: function (data) {

      if (data.status == 'success') {
        mensajes('success', data.msj);

      } else {
        mensajes('error', data.msj);
      }

    },
    error: function (xhr, status) {
      alert("Existió un problema");
    },
  });
}
function buscarConglomerada() {
  if ($('#desde').val() == '') {
    return mensajes('warning', 'Debe ingresar la fecha de inicio...');
  } else if ($('#hasta').val() == '') {
    return mensajes('warning', 'Debe ingresar la fecha de final...');
  }
  $('#tableVentasConglomerada').dataTable().fnClearTable();
  $('#tableVentasConglomerada').dataTable().fnDestroy();
  $('#tableVentasConglomerada').DataTable({
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
    order: [[5, "desc"]],
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
        "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "/conglomeradajs",
      type: "post",
      data: {
        'desde': $('#desde').val(),
        'hasta': $('#hasta').val(),
      },
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
    },
    columns: [
      {
        data: "id",
      },
      {
        data: "nro_comprobante",
      },
      {
        data: "tipo",
      },
      {
        data: "fecha",
      },
      {
        data: "nombreCliente",
      },
      {
        data: "nombres_paciente",
      },
      {
        data: "nombreSeguro",
      },
      {
        data: "procedimientos",
      },
      {
        className: "text-center",
        data: "fecha_procedimiento",
        render: function (data, type, row) {

          if (data == '0000-00-00 00:00:00') {
            return '';
          } else {
            return data;
          }
        },
      },
      {
        data: "nombreCrea",
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {
          if (row.estado == 0) {
            return row.nombreAnula;
          } else {
            return "";
          }
        },
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {

          if (row.electronica == 0) {
            return "No";
          } else if (row.estado_electronica == 3) {
            return "Autorizado";
          } else {
            return "";
          }
        },
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {
          var accion =
            '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
          var descargas = "";
          if (row.estado == 1) {
            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
          }
          if (row.doc_electronico == 5) {
            descargas = '<a href="javascvript:;" class="dropdown-item"><i class="fas fa-file-code" style="color:red;"></i> Descargar XML</a><a href="javascvript:;" class="dropdown-item"><i class="fas fa-file-pdf" style="color:red;"></i> Descargar PDF</a>';
          }
          var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verFacturaConglomerada(' + row.id + ');"><i class="fas fa-eye" style="color:green;"></i> Ver</a>' + descargas + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="anularConglomerada(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
          return botones;
        },
      },
    ],
  });
}
function buscarFacturadeCaja() {
  if ($('#desde').val() == '') {
    return mensajes('warning', 'Debe ingresar la fecha de inicio...');
  } else if ($('#hasta').val() == '') {
    return mensajes('warning', 'Debe ingresar la fecha de final...');
  }
  $('#tableFacturaCaja').dataTable().fnClearTable();
  $('#tableFacturaCaja').dataTable().fnDestroy();
  $('#tableFacturaCaja').DataTable({
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
    paginate: false,
    info: false,
    responsive: false,
    lengthChange: true,
    autoWidth: true,
    order: [[0, "desc"]],
    iDisplayLength: 1000,
    dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
    buttons: [
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
      url: "v3/" + $("#controlador").val() + "/facturar/" + $("#extra_").val() + "js",
      type: "post",
      data: {
        'desde': $('#desde').val(),
        'hasta': $('#hasta').val(),
        'id_paciente': $('#id_paciente').val(),
        'id_doctor': $('#id_doctor').val(),
        'tipo': $('#tipo').val(),
      },
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
    },
    columns: [
      {
        className: "text-center",
        data: "n_comprobante",
        render: function (data, type, row) {

          var botones = '<div class="btn-group"><button type="button" class="btn btn-' +
            $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' +
            $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="pdfFacturaCaja(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fa fa-upload" style="color:blue"></i></i>Descargar</a><a class="dropdown-item" href="javascript:;" onclick="eliminarFacturaCaja(\'' + row.id + '\',\'' + row.numero + '\');"><i class="fas fa-trash-alt" style="color:#00A65A"></i></i> Eliminar</a><a class="dropdown-item" href="javascript:;" onclick="EditarFacturaCaja(\'' + row.id + '\');"><i class="fas fa-edit" style="color:#F39C12"></i></i>Editar</a>' + "</div></div></div>";
          return botones;
        },
      },
      {
        data: "fechaCita",
      },
      {
        className: "text-center",
        data: "horaCita",
      },
      {
        data: "nombres",
      },
      {
        className: "text-center",
        data: "cortesia",
      },
      {
        data: "admision",
      },
      {
        data: "doctor",
      },
      {
        data: "procedimiento",
      },
      {
        className: "text-center",
        data: "seguro",
      },
      {
        className: "text-center",
        data: "id",
      },
      {
        className: "text-center",
        data: "nombre_tipo",
      },
      {
        className: "text-right",
        data: "efectivo",

      },
      {
        className: "text-right",
        data: "tarjeta",
      },

      {
        className: "text-right",
        data: "deposito",
      },
      {
        className: "text-right",
        data: "cheque",

      },
      {
        className: "text-right",
        data: "datalink",
      },
      {
        className: "text-right",
        data: "pagoapp",
      },
      {
        className: "text-right",
        data: "valor_oda",
      },
      {
        className: "text-right",
        data: "total_suma",
      },
      {
        className: "text-right",
        data: "total_suma",
      },
      {
        className: "text-center",
        data: "",
        render: function (data, type, row) {

          var botones = '<td>' +
            '<a class="btn btn-primary btn-xs" onclick="EditarFacturaCaja(\'' + row.id + '\');">' +
            '<i class="fas fa-edit" aria-hidden="true"></i></a>' +
            '<br>' +
            '<a class="btn btn-success btn-xs" onclick="EditarFacturaCaja(\'' + row.id + '\');">' +
            '<i class="fas fa-edit" aria-hidden="true"></i></a>' +
            '</td>';

          $('#Efectivo').text(row.total_global_efectivo);
          $('#Tarjeta').text(row.total_global_tarjeta);
          $('#deposito').text(row.total_global_deposito);
          $('#cheque').text(row.total_global_cheque);
          $('#datalink').text(row.total_global_datalink);
          $('#pago_app').text(row.total_global_app);
          $('#seguro').text(row.total_valor_oda);
          $('#total_vta').text(row.total_global_total);
          $('#honor_medicos').text(row.total_global_total);

          return botones;
        }
      },

    ],
  });
}
function cambiarEstadoVenta(id, estado) {
  Swal.fire({
    title: '¿Desea Anular este venta?',
    text: 'No puedes revertir esta acccion!',
    icon: 'error',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: "Si"
  }).then((result) => {
    if (result.isConfirmed) {
      var mensaje = "";
      let msj_ingr = "";
      let msj_ret = "";
      let msj_cruce = "";
      let msj_che = "";
      let msj_cruce_cue = "";
      let msj_credito = "";
      let mensajeaux = "";
      $.ajax({
        type: 'get',
        url: "v3/" + $('#controlador').val() + "/anularVentaVerificar",
        datatype: 'json',
        data: {
          'id_venta': id
        },
        success: function (data) {
          if (data.status == "error") {
            return mensajes("error", data.msj);
          }
          if (data.status == "success") {
            if (typeof (data.content.ingreso) != "undefined") {
              if (data.content.ingreso.length > 0) {
                for (let i = 0; i < data.content.ingreso.length; i++) {
                  mensaje = `${data.content.ingreso[i]}`;
                }
                msj_ingr = ` Ingreso: ${mensaje}`;
                mensaje = "ok";
              }
            }
            if (typeof (data.content.retencion) != "undefined") {
              msj_ret = ` Retencion: ${data.content.retencion}`;
              mensaje = "ok";
            }
            if (typeof (data.content.cruce) != "undefined") {
              if (data.content.cruce.length > 0) {
                for (let i = 0; i < data.content.cruce.length; i++) {
                  mensaje = `${data.content.cruce[i]}`;
                }
                msj_cruce = ` Cruces: ${mensaje}`;
                mensaje = "ok";
              }
            }
            if (typeof (data.content.chequepost) != "undefined") {
              for (let i = 0; i < data.content.chequepost.length; i++) {
                mensaje = `${data.content.chequepost[i]}`;
              }
              msj_che = ` Chequepost: ${mensaje}`;
              mensaje = "ok";
            }
            if (typeof (data.content.cruce_cuentas) != "undefined") {
              if (data.content.cruce_cuentas.length > 0) {
                for (let i = 0; i < data.content.cruce_cuentas.length; i++) {
                  mensaje = `${data.content.cruce_cuentas[i]}`;
                }
                msj_cruce = ` Cruce cuentas: ${mensaje}`;
                mensaje = "ok";
              }
            }
            if (typeof (data.content.credito) != "undefined" && data.content.credito.length > 0) {
              for (let i = 0; i < data.content.credito.length; i++) {
                mensaje = `${data.content.credito[i]}`;
              }
              msj_credito = ` Credito: ${mensaje}`;
              mensaje = "ok";
            }

            if (mensaje != "") {
              return mensajes("warning", `Existen algunos observaciones encontradas: ${msj_ingr}${msj_ret}${msj_cruce}${msj_che}${msj_cruce}${msj_credito}`)
            } else {
              testVenta(id);
            }
          }
        },
        error: function (data) {
          console.log(data);
        }
      });
    }
  })
}
async function testVenta(id, estado) {
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
        url: "v3/" + $('#controlador').val() + "/anularVentaD",
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
function eliminarFacturaCaja(id) {
  var data = {
    id: id
  };
  var json = ajax("v3/ventas/eliminarFacturaCaja", data, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    mensajes(json.status, json.message);
    if (json.status === 'success') {
      $('#tableFacturaCaja').DataTable().ajax.reload();
    }
  }
}
function pdfFacturaCaja(id) {
  window.open($("#base_url").val() + "/v3/ventas/pdfFacturaCaja?id=" + id, "_blank");
}
function EditarFacturaCaja(id) {
  window.open($("#base_url").val() + "/v3/ventas/EditarFacturaCaja?id=" + id + '&valor=' + 0, "_blank");
}
function buscarVentas() {
  if ($('#desde').val() == '') {
    return mensajes('warning', 'Debe ingresar la fecha de inicio...');
  } else if ($('#hasta').val() == '') {
    return mensajes('warning', 'Debe ingresar la fecha de final...');
  }
  var id_cliente = $('#id_cliente').val().split('|');
  $('#tableVentas').dataTable().fnClearTable();
  $('#tableVentas').dataTable().fnDestroy();
  $("#tableVentas").DataTable({
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
    "order": [
      [6, 'desc'], // Ordena por la primera columna (fecha) en orden descendente
      [0, 'desc'] // Luego ordena por la segunda columna (comprobante) en orden descendente
    ],
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
        exportOptions: {
          columns: ':not(:eq(14))' // Excluir la columna en el índice 2
        }
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
        "v3/" + $("#controlador").val() + "/buscarVentas",
      type: "post",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      data: {
        d: $('#desde').val(),
        h: $('#hasta').val(),
        c: id_cliente[0]
      },
      error(error) {
        console.log(error);
      },
    },
    columns: [
      {
        data: "nro_comprobante",
      },
      {
        data: "nombre_cliente",
      },
      {
        className: "text-center",
        data: "id",
      },
      {
        className: "text-center",
        data: "id_asiento",
        render: function (data, type, row) {
          return '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + '\')">' + row.id_asiento + '</a>';
        }
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {
          var estado = "";
          if (row.doc_electronico == -1) {
            estado = '<a href="#" title="Pendiente"><i class="fa-solid fa-circle" style="color:yellow;width:16px;"></i></a>';
          } else if (row.doc_electronico == 0) {
            estado = '<a href="#" title="Sin autorizar"><i class="fa-solid fa-circle" style="color:yellow;width:16px;"></i></a>';
          } else if (row.doc_electronico == 1) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          } else if (row.doc_electronico == 2) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          } else if (row.doc_electronico == 3) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          } else if (row.doc_electronico == 4) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          } else if (row.doc_electronico == 5) {
            estado = '<a href="#" title="Autorizado"><i class="fa-solid fa-circle" style="color:green;width:16px;"></i></a>';
          } else if (row.doc_electronico == 6) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          } else if (row.doc_electronico == 7) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          } else if (row.doc_electronico == 8) {
            estado = '<a href="#"><img src="' + $("#base_url").val() + '/v3/dist/img/puntoamarillo.png" style="width:18px;" title="Sin autorizar"></a>';
          }
          return estado;
        },
      },
      {
        data: "tipo",
      },
      {
        className: 'text-center',
        data: "fecha",
      },
      {
        data: "nombres_paciente",
      },
      {
        data: "seguro",
      },
      {
        data: "procedimientos",
      },
      {
        className: 'text-center',
        data: "fecha_procedimiento",
        render: function (data, type, row) {
          var fecha = row.fecha_procedimiento !== null ? getfecha(row.fecha_procedimiento, null, '-') : '';
          return fecha;
        }
      },
      {
        className: 'text-center',
        data: "estado",
        render: function (data, type, row) {
          var estado =
            row.estado == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
          return estado;
        },
      },
      {
        className: "text-center",
        data: "usuariocrea",
      },
      {
        className: "text-center",
        data: "usuariomod",
      },
      {
        className: "text-center",
        data: "id",
        render: function (data, type, row) {
          var accion = '<i class="fas fa-times-circle" style="color:gray;"></i> Anulado';
          var descargas = "";
          if (row.estado == 1) {
            accion = '<i class="fas fa-trash-alt" style="color:red;"></i> Anular';
          }
          if (row.doc_electronico == 5) {
            descargas = '<a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=descargarXML&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-code" style="color:green;"></i> Descargar XML</a><a href="' + $('#base_url').val() + '/api_doc_electronico?opcion=generarPdf&clave=' + row.nro_autorizacion + '" class="dropdown-item"><i class="fas fa-file-pdf" style="color:red;"></i> Descargar PDF</a>';
          }
          var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verFactura(\'' + row.id + "','" + row.nro_comprobante + '\');"><i class="fas fa-eye" style="color:green;"></i> Ver</a>' + descargas + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstadoVenta(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
          return botones;
        },
      },
      {
        data: "nro_autorizacion",
        visible: false
      },
      {
        data: "id_cliente",
        visible: false
      },
      {
        data: "concepto",
        visible: false
      },
      {
        data: "subtotal_12",
        visible: false
      },
      {
        data: "subtotal_0",
        visible: false
      },
      {
        data: "subtotal_total",
        visible: false,
      },
      {
        data: "impuesto",
        visible: false,
      },
      {
        data: "total_final",
        visible: false,
      },
    ],
  });
}
let sharedData;
function abrirModal(id) {
  let json = ajax("v3/" + $("#controlador").val() + "/traerDatosClientes", { "identificacion": id }, 'json');
  if (json.responseJSON != '') {
    json = json.responseJSON;
    idProvincia = json.provincia_representante;
    idCiudad = json.id_ciudad;
    $("#nombre").val(json.nombre);
    $("#email_representante").val(json.email_representante);
    $("#identificacion").val(json.identificacion);
    $("#clase").val(json.clase).trigger("change");
    $("#tipo_identificacion").val(json.tipo).trigger("change");
    $("#telefono1_representante").val(json.telefono1_representante);
    $("#telefono2_representante").val(json.telefono2_representante);
    let switchElement = document.getElementById("cheestado");
    if (json.estado == 1) {
      $("#estado").val(1);
      switchElement.checked = true;
    }
  }
  $("#pais").val(json.pais).trigger("change");
  $("#urbanizacion").val(json.urbanizacion);
  $("#etapa").val(json.etapa);
  $("#manzana").val(json.manzana);
  $("#villa").val(json.villa);
  $("#direccion").val(json.direccion);
  $("#referencia").val(json.referencia);
  $("#myModal").modal("toggle");
}
function nuevoFilaOrden() {
  let nuevafila = $("#mifila").html();
  let rowk = document.getElementById("agregar_fila").insertRow(-1);
  rowk.innerHTML = nuevafila;
  rowk.className = "well";
  $(rowk).find(".agg").addClass("select2_producto_fv");
  $(rowk).find(".agb").addClass("select2_bodega");
  $(rowk).find(".select2_producto_fv").select2({
    width: "100%",
    language: {
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarProducto",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        let results = response.map((item) => {
          let arreglo = item.data.split("|");
          let obj = {
            id: item.id,
            text: item.text,

            // data-name="{{$value->nombre}}" 
            // data-codigo="{{$value->codigo}}" 
            // data-descuento="{{$value->mod_desc}}" 
            // data-precio="{{$value->mod_precio}}" 
            // data-maxdesc="{{$value->descuento}}" 
            // data-iva="{{$value->iva}}"

            data: {
              codigo: arreglo[0],
              descuento: arreglo[1],
              precio: arreglo[2],
              maxdesc: arreglo[3],
              iva: arreglo[4],
            }
          };
          return obj;
        });

        return {
          results: results
        };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $(".select2_bodega").select2({
    placeholder: "--Seleccione--",
  });
}
$('body').on('click', '.cp', function () {
  let prevClass = $(this).prev().attr('class');
  let html = '<input type="text" class="form-control pneto" name="precio[]" onblur="this.value=parseFloat(this.value).toFixed(2);">' +
    '<button type="button" class="btn btn-info btn-gray btn-xs cp">' +
    '<i class="fa fa-pencil-alt" aria-hidden="true"></i>' +
    '</button>';
  if (prevClass.includes('select2_precio')) {
    $(this).parent().append(html);
    $(this).prev().remove();
    $(this).remove();

  } else {
    let html = '<select name="precio[]" class="form-control select2_precio pneto" style="width:100% !important" autofocus active required>' +
      '<option value="">--Seleccione--</option></select>' +
      '<button type="button" class="btn btn-info btn-gray btn-xs cp" >' +
      '<i class="fa fa-pencil-alt" aria-hidden="true"></i></button>';
    $(this).parent().append(html);
    verificarRC($(this).closest('tr').find('td').eq(0).find('select')[0]);
    $(this).prev().remove();
    $(this).remove();
  }
});
$('body').on('click', '.chef', function () {
  totalesRC(0);
});
$('body').on('click', '.delete', function () {
  $(this).parent().parent().remove();
  totalesRC(0);
});
$('body').on('change', '.desc', function () {
  let cant = $(this).closest('tr').find('td').find('input[name="cantidad[]"]').val();
  let precio = $(this).closest('tr').find('td').find('input[name="precio[]"]').val();
  let descuento = $(this).val();
  verificarRC($(this).closest('tr').find('td').eq(0).find('select')[0]);

  let pdesc = 0;
  if (cant == 0 || precio == 0) {
    pdesc = 0;
  } else {
    pdesc = (descuento * 100) / (parseInt(cant) * parseFloat(precio));
  }
  $(this).parent().prev().children().val(pdesc);
  let copago = $(this).closest('tr').find('td').find('input[name="copago[]"]').val();
  let total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
  $(this).parent().next().children().val(total.toFixed(2));
  totalesRC(0);
});
function totalesRC(e) {
  var subt12 = [];
  var subt0 = [];
  var descuentosub0 = 0;
  var descuentosub12 = 0;
  var sb12 = 0;
  var sb0 = 0;
  var d = 0;
  var copagoTotal = 0;
  let cont = 0;
  if (e == 0) {
    $('.cneto').each(function () {
      let cant = $(this).val() || 0;
      let precio = $(this).closest('tr').find("td").find('input[name="precio[]"]').val() || $(this).closest('tr').find("td").find('select[name="precio[]"]').val() || 0;
      let copago = $(this).closest('tr').find("td").find('input[name="copago[]"]').val() || 0;
      let descuento = $(this).closest('tr').find("td").find('input[name="desc[]"]').val() || 0;
      d = parseFloat(d) + parseFloat(descuento);
      let iva = $(this).closest('tr').find('td').find('input[name="valoriva[]"]').prop('checked') || 0;
      precio = precio != null ? precio : 0;
      let total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago);
      total = redondeafinal(total, 2);
      if (iva) {
        subt12.push(total);
        sb12 = sb12 + total;
        descuentosub12 += parseFloat(descuento);
      } else {
        subt0.push(total);
        sb0 = sb0 + total;
        descuentosub0 += parseFloat(descuento);
      }
      copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);
      $("#subtotal_12").html(sb12.toFixed(2));
      $("#subtotal_0").html(sb0.toFixed(2));
      $("#descuento").html(d.toFixed(2));
      let descuento_total = descuentosub12 + descuentosub0;
      let sum = sb12 + sb0 - descuento_total;
      sum = redondeafinal(sum);
      $("#base").html(sum.toFixed(2));
      let ivaS = $("#ivareal").val();
      let ti = ivaS * sb12;
      if (d > 0) {
        if (sb12 > 0) {
          ti = ivaS * (sb12 - descuentosub12);
        }
      }
      ti = redondeafinal(ti);
      $("#tarifa_iva").html(ti.toFixed(2));
      let totax = sum + ti;
      totax = redondeafinal(totax);
      $("#total").html(totax.toFixed(2));
      $("#copagoTotal").html(copagoTotal.toFixed(2));
      sb12 = redondeafinal(sb12);
      $("#subtotal_121").val(sb12);
      sb0 = redondeafinal(sb0);
      d = redondeafinal(d);
      $("#subtotal_01").val(sb0);
      $("#descuento1").val(d);
      $("#tarifa_iva1").val(ti);
      $("#total1").val(totax);
      $("#totalc").val(copagoTotal.toFixed(2));
      cont++;
    });
  }
}
function verificarRC(selectElement) {
  if (selectElement.value !== "") {
    let iva = $(selectElement).select2('data')[0].data.iva ?? 0;
    let codigo = $(selectElement).select2('data')[0].data.codigo ?? 0;
    let usadescuento = $(selectElement).select2('data')[0].data.descuento ?? 0;
    let max = $(selectElement).select2('data')[0].data.mod_desc ?? 0;
    let modPrecio = $(selectElement).select2('data')[0].data.precio ?? 0;

    $(selectElement).parent().children().closest(".codigo_producto").val(codigo);
    $(selectElement).parent().children().closest(".iva").val(iva);

    if (!usadescuento) {
      $(selectElement).closest('tr').find('td');
      $(selectElement).closest('tr').find('td').find('input[name="descpor[]"]').attr("readonly", "true");
      $(selectElement).closest('tr').find('td').find('input[name="desc[]"]').attr("readonly", "true");
      $(selectElement).closest('tr').find('td').find('input[name="descpor[]"]').val(0);
      $(selectElement).closest('tr').find('td').find('input[name="desc[]"]').val(0);
    } else {
      $(selectElement).closest('tr').find('td').find('input[name="descpor[]"]').removeAttr("readonly");
      $(selectElement).closest('tr').find('td').find('input[name="descpor[]"]').removeAttr("readonly");

    }
    $(selectElement).closest('tr').find('td').find('.maxdesc').val(max);
    if (iva == '1') {
      $(selectElement).closest('tr').find('td').find('input[name="valoriva[]"]').attr("checked", "checked");
    }
    let tipo = $("#tipo_cliente").val();
    $.ajax({
      type: 'post',
      url: "v3/" + $("#controlador").val() + "/preciosNivel",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        id: codigo
      },
      success: function (data) {
        totalesRC(0);

        $.each(data, function (key, value) {
          if (tipo == value.nivel) {
            selected = "selected";
          } else {
            selected = "";
          }
          $(selectElement).closest('tr').find('td').find(".pneto").empty();
          $(selectElement).closest('tr').find('td').find(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
        });
      },
      error: function (data) { }
    });
  }
}
$("#btn_pagoRC").on("click", function () {
  id = document.getElementById('contador_pago').value;
  var midiv_pago = document.createElement("tr")
  midiv_pago.setAttribute("id", "dato_pago" + id);
  let fecha = new Date().toISOString().split('T')[0];
  midiv_pago.innerHTML = `
        <td>
          <select class="form-control pago selectMetodoPago" name="id_pago[]" id="id_pago_${id}" style="width: 100%;" onchange="revisar_componentes(this,${id});">
        </td>
        <td>
          <input type="date" class="form-control input-number" value="${fecha}" name="fecha_pago[]" id="fecha_pago_${id}">
        </td>
        <td>
          <select class="form control selectTarjeta"  id="id_tarjetas_${id}" name="id_tarjetas[]" style="width: 100%;"></select>
        </td>
        <td>
          <input type="text" class="form-control" name="numero[]" id="numero_${id}">
        </td>
        <td>
          <select class="form-control selectBanco" name="id_bancos[]" id="id_bancos_${id}"></select>
          </td>
          <td>
            <input  class="form-control" name="cuenta[]" id="cuenta_${id}" >
          </td>
          <td>
            <input class="form-control" type="text" id="giradoA_${id}" name="giradoA[]">
          </td>
          <td>
            <input class="form-control text-right input-number fpago" type="text" id="valor_${id}" name="valor[]" onblur="this.value=parseFloat(this.value).toFixed(2);"  value="0" onchange="revision_total(${id})" onkeypress="return soloNumeros(this);">
          </td>
          <td >
            <button style="text-align:center;" type="button"  class="btn btn-danger btn-gray delete btn-xs">
            <i class="fa fa-trash" aria-hidden="true"></i>
            </button>
          </td>`;

  document.getElementById('agregar_pago').appendChild(midiv_pago);
  id = parseInt(id);
  id = id + 1;
  document.getElementById('contador_pago').value = id;


  $(".selectMetodoPago").select2({
    placeholder: "Seleccione un método de pago",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayMetodoPago').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      };
    }))
  });


  $(".selectTarjeta").select2({
    width: "100%",
    placeholder: "Tipo Tarjeta",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayMetodoTarjeta').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      }
    }))
  });



  $(".selectBanco").select2({
    width: "100%",
    placeholder: "Seleccione un banco",
    data: [{
      text: "Seleccione",
      id: ""
    }].concat($.map(JSON.parse($('#arrayMetodoBanco').val()), function (method) {
      return {
        text: method.nombre,
        id: method.id,
      }
    }))
  });
});
$('body').on('change', '.pdesc', function () {
  var cant = $(this).parent().prev().prev().prev().children().val();
  var precio = $(this).parent().prev().prev().children().val();
  var pdesc = $(this).val();
  var descuento = (parseInt(cant) * parseFloat(precio)) * pdesc / 100;
  descuento = redondeafinal(descuento);
  $(this).parent().next().children().val(descuento);
  var copago = $(this).parent().prev().children().val();
  var total = (parseInt(cant) * parseFloat(precio)) - descuento - copago;
  total = redondeafinal(total);
  $(this).parent().next().next().children().val(total);
  totalesRC(0);
});
function guardarVentaRC() {
  if ($("#sucursal_empre").val() == "") {
    return mensajes("warning", "Seleccione una sucursal para continuar...");
  } else if ($("#punto_emision").val() == "") {
    return mensajes("warning", "Seleccione un punto de emisión para continuar...");
  } else if ($("#punto_emision").val() == "") {
    return mensajes("warning", "Seleccione un punto de emisión para continuar...");
  } else if ($("#divisas").val() == "") {
    return mensajes("warning", "Seleccione la divisa para continuar...");
  } else if ($("#procedimiento").val() == "") {
    return mensajes("warning", "Seleccione un procedimiento para continuar...");
  } else if ($("#agregar_pago").length == 0) {
    return mensajes("warning", "Seleccione un método de pago para continuar...");
  } else if ($("#agregar_fila").length == 0) {
    return mensajes("warning", "Seleccione un producto para continuar...");
  } else {
    mensajes("question", $("#pregunta").val(), "frm_recibo_cobro", "action");
  }
}
function formatNumber(value) {
  let number = parseFloat(value);
  return isNaN(number) ? 0 : (Number.isInteger(number) ? number : number.toFixed(2));
}
$(document).on('click', '#btnagregarServicio', function (e, p = "") {
  let contador = parseFloat($("#contador").val()) + 1;
  let nuevaFila = `
      <tr id="fila${p == "" ? contador : contador.toString() + p.toString()}" class="no-style">
        <td style="height: 20px;">
            <select class="form-control select2_producto_ventas" id="id_producto${contador}" name="id_producto[]">
                <option value="">--Seleccione--</option>
            </select>
        </td>
        <td></td>
        <td style="text-align: center;">
            <input type="text" name="cantidad[]" style="width: 100%;" class="calcular form-control text-right" id="cantidad${contador}">
        </td>
        <td style="text-align: center;">Unidad</td>
        <td style="text-align: center;"><input type="text" style="width: 100%;" id="unidad${contador}" name="unidad[]" class="calcular form-control  text-right"></td>
        <td></td>
        <td style="text-align: center;">
            <select id="iva${contador}" style="width: auto;height: auto;" name="iva[]" class="form-control calcular">
                <option value="">Seleccionar</option>
                <option value="0.15">15%</option>
                <option value="0.05">5%</option>
                <option value="0">0%</option>
                <option value="0">Excento</option>
            </select>
        </td>
        <td></td>
        <td>
            <div class="input-group mb-3">
                <input style="height: 28px;" type="text" id="retir${contador}" name="retir[]" class="form-control">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="cursor: pointer;" data-toggle="modal" data-target="#searchModalIr">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>
        </td>
        <td></td>
        <td>
            <div class="input-group mb-3">
                <input style="height: 28px;" type="text" id="rtiva${contador}" name="rtiva[]" class="calcular form-control">
                <div class="input-group-prepend">
                    <span class="input-group-text" style="cursor: pointer;" data-toggle="modal" data-target="#searchModalIva">
                        <i class="fas fa-search"></i>
                    </span>
                </div>
            </div>
        </td>
        <td>&nbsp;</td>
        <td style="text-align: center;"><input type="text" id="descuento1${contador}" name="descuento1[]" style="width: 80%;" class="calcular form-control text-right"></td>
        <td style="text-align: center;"><input type="text" id="descuento2${contador}" name="descuento2[]" style="width: 80%;" class="calcular form-control text-right"></td>
        <td style="text-align: center;"><input type="text" id="subtotal0" name="subtotal[]" style="width: 80%;" class="form-control text-right"></td>
        <td style="text-align: center;" ><button class="btn" type="button" onclick="eliminar(${contador},${p})"><i class="nav-icon fa fa-trash" style="color: red;"></i></button></td>
      </tr>
  `;
  $('#detalleFactura').append(nuevaFila);
  $(".select2_producto_ventas").select2({
    width: '280px',
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarProducto",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {

        const results = response.map(item => ({
          id: item.id,
          text: item.text,

        }));

        return {
          results: results
        };
      },
      error(error) {
        ""
        console.log(error + " asds");
      },
      cache: true,
    },
  });
  $("#contador").val(parseFloat(contador));
});
$(document).on('change', '.calcular', function (e) {
  let campoNombre = $(e.target).attr('name');
  let nombreCortado = campoNombre.replaceAll('[]', '');
  let cantidad = parseFloat($(this).closest('tr').find('td').find('input[name="cantidad[]"]').val()) || 0;
  let unidad = parseFloat($(this).closest('tr').find('td').find('input[name="unidad[]"]').val()) || 0;
  let descuento1 = parseFloat($(this).closest('tr').find('td').find('input[name="descuento1[]"]').val()) || 0;
  let descuento2 = parseFloat($(this).closest('tr').find('td').find('input[name="descuento2[]"]').val()) || 0;

  let subtotal = cantidad * unidad;
  let porcentajeDescuento = 0;
  let calculoDescuento = 0;
  if (nombreCortado == "descuento1") {
    calculoDescuento = (subtotal * descuento1) / 100;
    porcentajeDescuento = descuento1;
  } else if (nombreCortado == "descuento2") {
    calculoDescuento = descuento2;
    porcentajeDescuento = (calculoDescuento / subtotal) * 100;
  } else {
    calculoDescuento = descuento2;
    porcentajeDescuento = (calculoDescuento / subtotal) * 100;
  }
  subtotal -= calculoDescuento;
  $(this).closest('tr').find('td').find('[name="subtotal[]"]').val(formatNumber(subtotal));
  $(this).closest('tr').find('td').find('[name="descuento1[]"]').val(formatNumber(porcentajeDescuento));
  $(this).closest('tr').find('td').find('[name="descuento2[]"]').val(formatNumber(calculoDescuento));
  let contador = $("#contador").val();
  let subtotal0 = 0;
  let subtototal15 = 0;
  let subtototal5 = 0;
  let ivaSum = 0;
  let ivaSumC = 0;
  let totalT = 0;
  for (let index = 0; index <= contador; index++) {
    let cantidadS = $("#cantidad" + index).val() || 0;
    let unidadS = $("#unidad" + index).val() || 0;
    let ivaS = $("#iva" + index).val() || 0;
    let descuento = $("#descuento2" + index).val() || 0;
    let subtototalS = parseFloat((cantidadS * unidadS) - descuento);
    if (ivaS == 0.15) {
      subtototal15 += subtototalS;
      ivaSum += subtototalS * ivaS;
    } else if (ivaS == 0.05) {
      subtototal5 += subtototalS;
      ivaSumC += subtototalS * ivaS;
    } else {
      subtotal0 += subtototalS;
    }
    totalT = parseFloat(subtotal0 + subtototal15 + subtototal5 + ivaSum + ivaSumC);
  }
  $("#subtotal_5").val(subtototal5.toFixed(2));
  $("#subtotal_0").val(subtotal0.toFixed(2));
  $("#subtotal_12").val(subtototal15.toFixed(2));
  $("#iva15").val(ivaSum.toFixed(2));
  $("#iva5").val(ivaSumC.toFixed(2));
  $("#total").val(totalT.toFixed(2));
});
function formatNumber(value) {
  let number = parseFloat(value);
  return isNaN(number) ? 0 : (Number.isInteger(number) ? number : number.toFixed(2));
}
function eliminar(index, orden = '') {
  let contador = parseFloat($("#contador").val());
  $("#fila" + index + orden).remove();
  let indice = arregloVerificar.indexOf(orden);
  if (indice !== -1) {
    arregloVerificar.splice(indice, 1);
  }
  $("#contador").val(contador - 1);
  $('.calcular').trigger('change');
}

function verificar_tipo(e) {
  if ($(e).val() == 'FAC-CO') {
    $("#modal_conglomerada").modal("show");
  } else if ($(e).val() == 'FAC-AG') {
    $("#facturaAgrupadaModal").modal("show");
  }
}
function buscarModal() {
  let desde = "2023-01-01";   //$("#desde_modal").val();
  let hasta = "2024-09-23" //$("#hasta_modal").val();
  let seguro = $("#seguro_modal").val();
  let tipo_servicio = 3 //$("#tipo_servicio").val();
  if (desde == "") {
    return mensajes("warning", "Ingrese la fecha desde");
  } else if (hasta == "") {
    return mensajes("warning", "Ingrese la fecha hasta");
  }

  let data = {
    "desde": desde,
    "hasta": hasta,
    "seguro": seguro,
    "tipo_servicio": tipo_servicio
  };

  var html = ajax("v3/" + $("#controlador").val() + "/traerTiposServicios", data, "html")
  html = html.responseText;
  if (html !== "") {
    $("#tablaTipos").html(html);

    verificarCheck();
    totalesModal();
  }

}

function verificarCheck() {
  $(".verificarT").each(function () {
    let id = $(this).attr("id").replaceAll("check", "");
    if (!arregloVerificar.includes(parseInt(id))) {
      $(this).prop("checked", false);
    } else {
      $(this).prop("checked", true);
    }

  });
}



$('body').on('click', '.conglomerada', function () {
  let identificador = $(this).closest('tr').find('td').eq(2).find("input").eq(0).attr('class');
  let clase = $(this).closest('tr').find('td').eq(2).find('select').eq(0).attr('class') || identificador;
  var html = '<input type="text" class="form-control pneto" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]">' +
    '<button type="button" class="btn btn-info btn-gray btn-xs conglomerada">' +
    '<i class="fa fa-pencil" aria-hidden="true"></i>' +
    '</button>';
  if (clase.includes('select2_precio')) {
    $(this).parent().append(html);
    $(this).prev().remove();
    $(this).remove();
  } else {
    html = '<select  name="precio[]"  class="form-control select2_precio pneto" style="width:60%;display:inline;" autofocus active required>' +
      '<option value="0">0</option></select>' +
      '<button type="button" class="btn btn-info btn-gray btn-xs conglomerada" >' +
      '<i class="fa fa-pencil" aria-hidden="true"></i></button>';
    $(this).parent().append(html);
    verificarModal($(this).closest('tr').find('td').eq(0).find('select')[0]);
    $(this).prev().remove();
    $(this).remove();

  }
});


function verificarModal(selectElement) {
  if (selectElement.value !== "") {
    let codigo = $(selectElement).val();
    let tipo = $("#tipo_cliente").val();
    $.ajax({
      type: 'post',
      url: "v3/" + $("#controlador").val() + "/preciosNivel",
      headers: {
        'X-CSRF-TOKEN': $('input[name=_token]').val()
      },
      datatype: 'json',
      data: {
        id: codigo
      },
      success: function (data) {
        totalesRC(0);
        $(selectElement).closest('tr').find('td').find(".pneto").empty();
        $.each(data, function (key, value) {
          if (tipo == value.nivel) {
            selected = "selected";
          } else {
            selected = "";
          }
          $(selectElement).closest('tr').find('td').find(".pneto").append('<option value=' + value.precio + ' ' + selected + '>' + value.precio + '</option>');
        });
      },
      error: function (data) { }
    });
  }
  // controllare perche non funziona
  //$(".select2_precio").select2();
}


$(document).on("change", ".calcularModal", function () {
  totalesModal();
});


$(document).on("change", ".calcDesc", function () {

  let campoNombre = this.name;

  let porcentajeDescuento = 0;
  let calculoDescuento = 0;
  let nombreCortado = campoNombre.replaceAll('[]', '');
  let total = $(this).closest('tr').find("td").eq(6).find(".pfinal").val();

  if (nombreCortado == "descuento1") {
    calculoDescuento = (total * this.value) / 100;
    porcentajeDescuento = this.value;
  }
  if (nombreCortado == "descuento2") {
    calculoDescuento = this.value;
    porcentajeDescuento = (calculoDescuento / total) * 100;
  }
  $(this).closest('tr').find("td").eq(4).find('[name="descuento1[]"]').val(porcentajeDescuento);
  $(this).closest('tr').find("td").eq(5).find('[name="descuento2[]"]').val(calculoDescuento);
  totalesModal();
});


function totalesModal() {
  var sb12 = 0;
  var sb0 = 0;
  var subt12 = [];
  var d = 0;
  var subt0 = [];
  var descuentosub0 = 0;
  var descuentosub12 = 0;
  var copagoTotal = 0;
  $('.cneto').each(function (i, obj) {


    let cant = $(this).val() || 0;
    let precio = $(this).closest('tr').find("td").eq(2).find('.pneto').val();
    let copago = $(this).closest('tr').find("td").eq(3).find('.copago').val()
    let descuento = $(this).closest('tr').find("td").eq(5).find(".desc").val();



    d = parseFloat(d) + parseFloat(descuento);
    let iva = $(this).closest('tr').find('td').find('input[name="valoriva[]"]').prop('checked') || 0;
    precio = precio != null ? precio : 0;

    let total = (parseInt(cant) * (precio)) - parseFloat(0) - parseFloat(copago) - descuento;

    $(this).closest('tr').find("td").eq(6).find(".pfinal").val(total);
    if (iva) {
      subt12.push(total);
      sb12 = sb12 + total;
      descuentosub12 += parseFloat(descuento);
    } else {
      subt0.push(total);
      sb0 = sb0 + total;
      descuentosub0 += parseFloat(descuento);
    }
    copagoTotal = parseFloat(copagoTotal) + parseFloat(copago);

  });

}

function agregarFila(idOrden) {


  if (arregloVerificar.includes(idOrden)) {

    if ($("#contador").val() == 0) {
      let contador = $("#contador").val();
      $("#id_producto" + contador).val("").trigger("change");
      $("#cantidad" + contador).val("");
      $("#unidad" + contador).val("");
      $("#iva" + contador).val("");
      $("#descuento1" + contador).val("");
      $("#descuento2" + contador).val("");
      $("#subtotal" + contador).val("");
      let indice = arregloVerificar.indexOf(idOrden);
      arregloVerificar.splice(indice, 1);
    } else {
      $("#detalleFactura tr").each(function () {
        let replace = this.id.replaceAll("fila", "");
        let splitString = replace.split("");
        let reverseArray = splitString.reverse();
        var joinArray = reverseArray.join("");
        let splitStringd = idOrden.toString().split("");
        let reverseArrayd = splitStringd.reverse();
        var joinArrayd = reverseArrayd.join("");
        let validacion = joinArray.substring(0, idOrden.toString().length);
        if (validacion == joinArrayd) {
          $("#fila" + replace).remove();
        }

        let indice = arregloVerificar.indexOf(idOrden);
        arregloVerificar.splice(indice, 1);
      });

    }

    $('.calcular').trigger('change');
    return;
  }

  let cantidadElementos = $("#entrega" + idOrden).find("td").find(".pfinal").length;




  if (cantidadElementos == 1) {
    let nombreProducto = $("#entrega" + idOrden).find("td").find('.select2_producto_ventas');
    let cantidad = $("#entrega" + idOrden).find("td").find('.cneto').val();
    let precio = $("#entrega" + idOrden).find("td").find(".pneto").val();
    let ivac = $("#entrega" + idOrden).find("td").find('input[name="valoriva[]"]').is(":checked");
    let descuento1 = $("#entrega" + idOrden).find("td").find('input[name="descuento1[]"]').val();
    let descuento2 = $("#entrega" + idOrden).find("td").find('input[name="descuento2[]"]').val();
    let precioFinal = $("#entrega" + idOrden).find("td").find('.pfinal').val();
    let newOption = new Option(nombreProducto.text(), nombreProducto.val(), false, true);



    if ($("#contador").val() == 0 && $("#id_producto" + $("#contador").val()).val() == "") {
      $("#fila" + $("#contador").val()).attr("id", "fila" + $("#contador").val() + idOrden);
      $('#id_producto' + $("#contador").val()).append(newOption).trigger('change');
      $("#cantidad" + $("#contador").val()).val(cantidad);
      $("#unidad" + $("#contador").val()).val(precio);
      if (ivac) {
        $("#iva" + $("#contador").val()).val("0.15");
      }
      $("#descuento1" + $("#contador").val()).val(descuento1);
      $("#descuento2" + $("#contador").val()).val(descuento2);
      $("#subtotal" + $("#contador").val()).val(precioFinal);

    } else {
      $("#btnagregarServicio").trigger("click", [idOrden]);
      $('#id_producto' + $("#contador").val()).append(newOption).trigger('change');
      $("#cantidad" + $("#contador").val()).val(cantidad);
      $("#unidad" + $("#contador").val()).val(precio);
      if (ivac) {
        $("#iva" + $("#contador").val()).val("0.15");
      }

      $("#descuento1" + $("#contador").val()).val(descuento1);
      $("#descuento2" + $("#contador").val()).val(descuento2);
      $("#subtotal" + $("#contador").val()).val(precioFinal);


    }
    $('.calcular').trigger('change');
    arregloVerificar.push(idOrden);
  } else {
    for (let index = 1; index <= cantidadElementos; index++) {

      let elemento = $("#entrega" + idOrden).find("tr")[index];



      let nombreProducto = $(elemento).find("td").find('.select2_producto_ventas');
      let cantidad = $(elemento).find("td").find('.cneto').val();
      let precio = $(elemento).find("td").find(".pneto").val();
      let ivac = $(elemento).find("td").find('input[name="valoriva[]"]').is(":checked");
      let descuento1 = $(elemento).find("td").find('input[name="descuento1[]"]').val();
      let descuento2 = $(elemento).find("td").find('input[name="descuento2[]"]').val();
      let precioFinal = $(elemento).find("td").find('.pfinal').val();
      let newOption = new Option(nombreProducto.text(), nombreProducto.val(), false, true);



      if ($("#contador").val() == 0 && $("#id_producto" + $("#contador").val()).val() == "") {
        $("#fila" + $("#contador").val()).attr("id", "fila" + $("#contador").val() + idOrden);
        $('#id_producto' + $("#contador").val()).append(newOption).trigger('change');
        $("#cantidad" + $("#contador").val()).val(cantidad);
        $("#unidad" + $("#contador").val()).val(precio);
        if (ivac) {
          $("#iva" + $("#contador").val()).val("0.15");
        }
        $("#descuento1" + $("#contador").val()).val(descuento1);
        $("#descuento2" + $("#contador").val()).val(descuento2);
        $("#subtotal" + $("#contador").val()).val(precioFinal);

      } else {
        if (cantidad !== undefined) {
          $("#btnagregarServicio").trigger("click", [idOrden]);
          $("#fila").attr("id", "fila" + idOrden);
          $('#id_producto' + $("#contador").val()).append(newOption).trigger('change');
          $("#cantidad" + $("#contador").val()).val(cantidad);
          $("#unidad" + $("#contador").val()).val(precio);
          if (ivac) {
            $("#iva" + $("#contador").val()).val("0.15");
          }
          $("#descuento1" + $("#contador").val()).val(descuento1);
          $("#descuento2" + $("#contador").val()).val(descuento2);
          $("#subtotal" + $("#contador").val()).val(precioFinal);

        }
      }
      $('.calcular').trigger('change');
      arregloVerificar.push(idOrden);
    }
  }


}

function buscarModalFactura() {
  let desde = "2023-09-01";   //$("#desde_modal").val();
  let hasta = "2024-09-23" //$("#hasta_modal").val();
  let seguro = $("#seguro_modal").val();
  let tipo_servicio = null //$("#tipo").val();
  let usuario = $("#usuario").val();
  let n_orden = $("#n_orden").val();
  if (desde == "") {
    return mensajes("warning", "Ingrese la fecha desde");
  } else if (hasta == "") {
    return mensajes("warning", "Ingrese la fecha hasta");
  }

  let collect = {
    "desde": desde,
    "hasta": hasta,
    "seguro": seguro,
    "tipo_servicio": tipo_servicio,
    "usuario": usuario,
    "n_orden": n_orden,
  };

  $('#examples2').DataTable().clear().destroy();
  $('#examples2').DataTable({
    "searching": false,
    paging: false,
    processing: true,
    serverSide: true,
    responsive: true,
    'language': {
      "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
    },
    "ajax": {
      "url": "v3/" + $("#controlador").val() + "/traerPFacturaAgrupada",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      "type": "POST",
      "data": function (d) {
        d.collect = collect
      },
      "error": function (xhr, error, code) {
        console.log("Error en la llamada AJAX: ", error);
      }
    },

    "columns": [{
      "data": "id_orden",
      "orderable": false
    }, {
      "data": "fecha"
    },
    {
      "data": function (data) {
        const nivel = data.nivelDocs !== null ? data.nivelDocs.nombre : '';
        return `${data.seguro} <br/> ${nivel}`;
      },

      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {

        const cedula = data.orden.id_paciente !== null ? data.orden.id_paciente : '';
        const paciente = data.paciente;
        return `${paciente} <br> ${cedula}`;
      },

      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var dataEfectivo = 0;
        for (i = 0; i < data.length; i++) {
          dataEfectivo += data[i].dataEfectivo;
        }
        if (data.facturado == "Cierre Caja") {

          return dataEfectivo;
        }
        dataEfectivo = data.dataEfectivo;
        return dataEfectivo;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {

      "data": function (data) {
        var dataCheque = 0;
        for (i = 0; i < data.length; i++) {
          dataCheque += data[i].dataCheque;
        }
        if (data.facturado == "Cierre Caja") {

          return dataCheque;
        }
        dataCheque = data.dataCheque;
        return dataCheque;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {

      "data": function (data) {
        var dataDeposito = 0;
        for (i = 0; i < data.length; i++) {
          dataDeposito += data[i].dataDeposito;
        }
        if (data.facturado == "Cierre Caja") {
          return dataDeposito;
        }
        dataDeposito = data.dataDeposito;
        return dataDeposito;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var dataTransferencia = 0;
        for (i = 0; i < data.length; i++) {
          dataTransferencia += data[i].dataTransferencia;
        }
        if (data.facturado == "Cierre Caja") {

          return dataTransferencia;
        }
        dataTransferencia = data.dataTransferencia;
        return dataTransferencia;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var dataTarjetaCredito = 0;
        for (i = 0; i < data.length; i++) {
          dataTarjetaCredito += data[i].dataTarjetaCredito;
        }
        if (data.facturado == "Cierre Caja") {

          return dataTarjetaCredito;
        }
        dataTarjetaCredito = data.dataTarjetaCredito;
        return dataTarjetaCredito;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var dataTarjetaDebito = 0;
        for (i = 0; i < data.length; i++) {
          dataTarjetaDebito += data[i].dataTarjetaDebito;
        }
        if (data.facturado == "Cierre Caja") {

          return dataTarjetaDebito;
        }
        dataTarjetaDebito = data.dataTarjetaDebito;
        return dataTarjetaDebito;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var pendientePago = 0;
        for (i = 0; i < data.length; i++) {
          pendientePago += data[i].dataPendientePago;
        }
        if (data.facturado == "Cierre Caja") {

          return pendientePago;
        }
        pendientePago = data.dataPendientePago;
        return pendientePago;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var online = 0;
        for (i = 0; i < data.length; i++) {
          online += data[i].online;
        }
        if (data.facturado == "Cierre Caja") {

          return online;
        }
        online = data.online;
        return online;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var dataDatalink = 0;
        for (i = 0; i < data.length; i++) {
          dataDatalink += data[i].dataDatalink;
        }
        if (data.facturado == "Cierre Caja") {
          return dataDatalink;
        }
        dataDatalink = data.dataDatalink;
        return dataDatalink;
      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        var oda = 0;
        for (i = 0; i < data.length; i++) {
          oda += data[i].oda;
        }
        if (data.facturado == "Cierre Caja") {

          return oda;
        }
        oda = data.oda;
        return oda;

      },
      render: $.fn.dataTable.render.number(',', '.', 2, ''),
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {

        let boton1 = '<a class="btn btn-danger btn-xs" onclick="subirDocumento(' + data.id_orden + ')" ><i class="fa fa-cloud-upload" aria-hidden="true"></i></a>';
        let boton2 = data.estadoDocs !== null ? '<a class="btn btn-warning btn-xs" target="_blank" onclick="redigirDocs(' + data.id_orden + ')"  ><i class="fa fa-eye" aria-hidden="true"></i></a>' : '';
        return ' <div style="text-align: center;">' + boton1 + '' + boton2 + '</div>';
      },
      "targets": 0,
      "orderable": false
    },
    {
      "data": "numero",
      "targets": 0,
      "orderable": false
    },
    {
      "data": "usuario",
      "targets": 0,
      "orderable": false
    },
    {
      "data": function (data) {
        buttonnew = "#";
        var check = "";
        var newfor = null;
        var ride = "-10";
        var c1 = "0";
        var c2 = "0";
        var c3 = "0";
        var url_observacion = "javascript:text_observacion(" + data.id_orden + ")";
        var observacion = '<a class="btn btn-success btn-xs" href="' + url_observacion + '"> <i class="fa fa-comment"></i></a>';
        var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i></a>';
        var buttonDividirOrden = '<a class="btn btn-warning btn-xs"  onclick="dividir_orden(' + data.id_orden + ')"> <i class="fa fa-clone"></i> DIVIDIR</a>';
        if (typeof (data.orden) == 'object') {
          var buttonnew = "{{url('contable/cierre_caja/orden/')}}/" + data.orden.id + "/" + "1";
          if (typeof (data.orden.id) == 'undefined') {
            buttonnew = "#";
            var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '""> <i class="fa fa-edit"> </i>';
          } else {
            bs = "#";
            var newbu2 = '<a style="@if(Auth::user()->id_tipo_usuario==5) display:none @endif" class="btn btn-danger btn-xs" href="' + bs + '""> SRI </a>';
            var newbu = '<a class="btn btn-danger btn-xs" href="' + buttonnew + '"" target="_blank"> <i class="fa fa-edit"> </i>';
            var newfor = "-1";
            if (data.orden.fecha_envio != null && data.orden.comprobante != null) {
              ride = data.orden.comprobante
              ride = ride.split('-');
              c1 = parseInt(ride[0]);
              c2 = parseInt(ride[1]);
              c3 = parseInt(ride[2]);
            } else {
              newfor = data.orden.id;
              check = "<input type='checkbox' class='ses' value='" + data.orden.id + "' >";
            }

          }
        }
        if (c1 != 0) {
          return ' <div style="text-align: center;"> ' + check + '  ' + newbu + ' ' + buttonDividirOrden + '</i> </a><a style="@if(Auth::user()->id_tipo_usuario==5) display:none @endif" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI</a> <a class="btn btn-warning btn-xs" onclick="pdf_ride(' + c1 + ',' + c2 + ',' + c3 + ')" > RIDE</a> ' + observacion + ' </div>';
        } else {
          return ' <div style="text-align: center;"> ' + check + '  ' + newbu + ' ' + buttonDividirOrden + '</i> </a><a style="@if(Auth::user()->id_tipo_usuario==5) display:none @endif" class="btn btn-info btn-xs" onclick="emitir_sri(' + newfor + ')" > SRI</a> ' + observacion + ' </div>';
        }

      },
      "targets": 0,
      "orderable": false
    }

    ],
    "order": [
      [1, 'asc']
    ],
    "footerCallback": function (row, data, start, end, display) {
      var api = this.api();
      var intVal = function (i) {
        return typeof i === 'string' ?
          i.replace(/[\$,]/g, '') * 1 :
          typeof i === 'number' ?
            i : 0;
      };
      var cases = 0;
      if (data.facturado == "Cierre Caja") {
        cases = 1;
      }
      total3 = 0;
      total4 = 0;
      total5 = 0;
      total6 = 0;
      total7 = 0;
      total8 = 0;
      total9 = 0;
      total10 = 0;
      total11 = 0;
      total12 = 0;
      for (i = 0; i < data.length; i++) {
        if (data[i].facturado != 'Cierre Caja') {
          total3 += parseFloat(data[i].dataEfectivo);
          total4 += parseFloat(data[i].dataCheque);
          total5 += parseFloat(data[i].dataDeposito);
          total6 += parseFloat(data[i].dataTransferencia);
          total7 += parseFloat(data[i].dataTarjetaCredito);
          total8 += parseFloat(data[i].dataTarjetaDebito);
          total9 += parseFloat(data[i].dataPendientePago);
          total10 += parseFloat(data[i].online);
          total11 += parseFloat(data[i].dataDatalink);
          total12 += parseFloat(data[i].oda);
        }

      }
      $(api.column(3).footer()).html(
        '<label>Totales</label>'
      );
      $(api.column(4).footer()).html(
        '$' + total3.toFixed(2, 2)
      );
      $(api.column(5).footer()).html(
        '$' + total4.toFixed(2, 2)
      );
      $(api.column(6).footer()).html(
        '$' + total5.toFixed(2, 2)
      );
      $(api.column(7).footer()).html(
        '$' + total6.toFixed(2, 2)
      );
      $(api.column(8).footer()).html(
        '$' + total7.toFixed(2, 2)
      );
      $(api.column(9).footer()).html(
        '$' + total8.toFixed(2, 2)
      );
      $(api.column(10).footer()).html(
        '$' + total9.toFixed(2, 2)
      );
      $(api.column(11).footer()).html(
        '$' + total10.toFixed(2, 2)
      );
      $(api.column(12).footer()).html(
        '$' + total11.toFixed(2, 2)
      );
      $(api.column(13).footer()).html(
        '$' + total12.toFixed(2, 2)
      );
      var finale = total3 + total4 + total5 + total6 + total7 + total8 + total9 + total10 + total11 + total12;

    }
  });

}


function vaciarCampo(id) {
  $("#id_producto" + id).val("").trigger("change");
  $("#cantidad" + id).val("");
  $("#unidad" + id).val("");
  $("#iva" + id).val("");
  $("#descuento1" + id).val("");
  $("#descuento2" + id).val("");
  $("#subtotal" + id).val("");
  arregloVerificar = [];
}
