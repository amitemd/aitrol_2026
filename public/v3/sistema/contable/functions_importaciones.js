var fila = 0;
$(document).ready(function () {
  try {

    $("#id_cuentas").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      dropdownParent: $("#modal_proveedores"),
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
      dropdownParent: $("#modal_proveedores"),
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


    let queryString = window.location.search;
    let params = new URLSearchParams(queryString);
    let id = params.get('id');
    let data = {
      "id": id
    };
    if ($("#tablekardexResumen").length) {
      var html = ajax("v3/" + $("#controlador").val() + "/resumenkardex", data, "html");
      html = html.responseText;
      if (html !== "") {
        $("#tablekardexResumen").html(html);
      }
    }
    $("#tableImportaciones").DataTable({
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
        }, {
          className: 'text-center',
          data: "secuencia_importacion",
        },
        {
          data: "razonsocial",
        },
        {
          data: "observacion",
        },
        {
          className: 'text-center',
          data: "fecha",
        },
        {
          className: 'text-center',
          data: "secuencia_factura",
        },
        {
          className: 'text-center',
          data: "serie",
        },
        {
          data: "usuariocrea",
        },
        {
          className: 'text-center',
          data: "id",
          render: function (data, type, row) {
            var inventario = row.inventario == 0 ? 'Pendiente' : 'Listo';
            return inventario;
          }
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) { 
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var factura = '<a class="dropdown-item" href="javascript:;" onclick="addFactura(\'' + row.id + '\');"><i class="fa fa-money" style="color:#28a745;"></i> Factura</a>';
            var recibo = '<a class="dropdown-item" href="javascript:;" onclick="addRecibo(\'' + row.id + '\');"><i class="fas fa-receipt" style="color: #0a4275;"></i> Recibo</a>';
            var orden = '<a class="dropdown-item" href="javascript:;" onclick="addOrden(\'' + row.id + '\');"><i class="fas fa-clipboard-list" style="color: #6c757d;"></i> Orden</a>';
            var liqsenae = '<a class="dropdown-item" href="javascript:;" onclick="addOrden(\'' + row.id + '\');"><i class="fas fa-file-invoice-dollar" style="color: #ff7e00;"></i> Liq/SENAE</a>';
            var lotefecha = '<a class="dropdown-item" href="javascript:;" onclick="addOrden(\'' + row.id + '\');"><i class="fas fa-calendar-times" style="color: #8B4513;"></i> Lote/F. Vencimiento</a>';
            var pdf = '<a class="dropdown-item" href="javascript:;" onclick="addpdfimportaciones(\'' + row.id + '\');"><i class="fas fa-file-pdf" style="color: #d32f2f;"></i> PDF</a>';
            var excel = '<a class="dropdown-item" href="javascript:;" onclick="addexcelimportaciones(\'' + row.id + '\');"><i class="fas fa-file-excel" style="color: #1f9d55;"></i> EXCEL</a>';
            var subirarchivo = '<a class="dropdown-item" href="javascript:;" onclick="addOrden(\'' + row.id + '\');"><i class="fas fa-cloud-upload-alt" style="color: #007bff;"></i> Subir archivo</a>';
            var kardex = '<a class="dropdown-item" href="javascript:;" onclick="kardexImportaciones(\'' + row.id + '\');"><i class="fas fa-archive" style="color: #1e5eab;"></i> Kardex</a>';
            var resumenkardex = '<a class="dropdown-item" href="javascript:;" onclick="resumenkardex(\'' + row.id + '\');"><i class="fas fa-clipboard-check" style="color: #007bff;"></i> Resumen kardex</a>';
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProducto(\'' + row.codigo + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a>' + factura + recibo + orden + liqsenae + lotefecha + pdf + excel + subirarchivo + kardex + resumenkardex + '<div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
  } catch (error) { }
  $("#termino").change(function () {
    var dias = parseInt($(this).val());
    if (!isNaN(dias)) {
      var fechaObj = new Date();
      fechaObj.setDate(fechaObj.getDate() + dias);
      var fechaFormateada = fechaObj.toISOString().split('T')[0];
      $("#f_caducidad").val(fechaFormateada);
    } else {
      mensajes('info', 'Seleccione una opción válida.');
    }
  });
  $("#agregarLinea").on("click", function () {
    var html = '<tr id="tr_' + fila + '">';
    html += '<td style="width: 18%;"><input type="hidden" class="form-control" id="baseImponible_' + fila + '" name="baseImponible[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_sin_' + fila + '" name="iva_fila_sin[]" readonly />' + '<input type="hidden" class="form-control" id="iva_fila_' + fila + '" name="iva_fila[]" readonly />';
    html += '<select style="width:100%;" class="form-control select2_producto" id="nombre_' + fila + '" onchange="asignarProducto(\'' + fila + '\')" name="producto[]">' + '<option value="">--Seleccione--</option></select><br/><textarea type="text" class="form-control text-right" id="detalle_' + fila + '" name="detalle[]" value="1" /></textarea></td>';
    html += '<td style="width: 8%;"><input type="text" class="form-control text-right" id="cantidad_' + fila + '" name="cantidad[]" value="1" onblur="calcular();" />';
    html += '<td style="width: 8%;"><input type="text" class="form-control text-right" id="precio_' + fila + '" name="precio[]" value="0.00" onblur="calcular();" /></td>';
    html += '<td style="width: 8%;"><input type="text" class="form-control text-right" id="descuento_' + fila + '" name="descuento[]" value="0.00" onblur="getPorcent(' + fila + ');" /></td>';
    html += '<td style="width: 8%;"><input type="text" class="form-control text-right" id="pdescuento_' + fila + '" name="pdescuento[]" value="0.00" onblur="getValue(' + fila + ');" /></td>';
    html += '<td style="width: 8%;"><input type="text" class="form-control text-right" id="total_' + fila + '" name="total[]" value="0.00" onblur="calcular();" /></td>';
    html += '<td style="text-align:center;width: 5%;"><div class="form-group clearfix"><div class="icheck-success d-inline"><input type="checkbox" id="valoriva' + fila + '" name="valoriva[]"><label for="valoriva' + fila + '"></label></div></div></td>';
    html += '<td align="center" style="width: 5%;"><i class="fas fa-trash-alt fa-2x" style="color:red;cursor:pointer;" onclick="eliminarFila(\'' + fila + "')\"></i></td>";
    html += '</tr>';
    fila++;
    calcular();
    $("#detalleCompra").before(html);
    $(".select2_producto").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      placeholder: "--Seleccione--",
      minimumInputLength: 3,
      ajax: {
        url: 'v3/' + $('#controlador').val() + '/buscarGastosImportacion',
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
  });
  $('#btnguardarFactura').click(function () {
    if ($('#observacion').val() == '') {
      return mensajes('info', 'Debe ingresar un concepto de la importación');
    }
    if ($('#secuencia_factura').val() == '') {
      return mensajes('info', 'Debe ingresar el secuencial del documento');
    } else {
      if (!validarProducto()) {
        mensajes('question', $('#pregunta').val());
      }
    }
  });
  $('#secuencia_factura').change(function () {
    var originalString = $('#secuencia_factura').val();
    var paddedString = PadLeft(originalString, 9);
    $('#secuencia_factura').val(paddedString);
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
  $(".select2_provi").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + "proveedores" + "/getProveedorCombo",
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
  $('#archivo_sri').change(function () {
    if ($('#archivo_sri').is(':checked')) {
      $('#archivosri').val(1);
    } else {
      $('#archivosri').val(0);
    }
  });
});

function addFactura(id) {
  window.open('v3/' + $('#controlador').val() + '/ingresa_factura?id=' + id, 'new');
}

function addpdfimportaciones(id) {
  window.open('v3/' + $('#controlador').val() + '/addpdfimportaciones?id=' + id, 'new');
}

function addexcelimportaciones(id) {
  window.open('v3/' + $('#controlador').val() + '/addexcelimportaciones?id=' + id, 'new');
}

function resumenkardex(id) {
  window.open('v3/' + $('#controlador').val() + '/vistaResumen?id=' + id, 'new');
}
function calcular() {
  var descuento = 0;
  var subtotal0 = 0;
  var subtotal12 = 0;
  var iva = 0;
  let baseImponibleTotal = 0;
  let baseImponible = 0;
  let ivasin = 0;
  let sumaivasin = 0;
  try {
    let cantidad = 0;
    let precio = 0;
    $("input[name='cantidad[]']").each((i, v) => {
      cantidad += Number(v.value);
    });
    $("input[name='precio[]']").each((i, v) => {
      let precioUni = Number(v.value) * Number($("#cantidad_" + i).val());
      let is_iva = $("#nombre_" + i).val().split("|");
      descuento += Number($("#descuento_" + i).val());
      if (is_iva.length > 3) {
        if (is_iva[3] == 1) {
          ivasin = Number(v.value) * Number($("#cantidad_" + i).val());
          sumaivasin += ivasin;
          subtotal12 += precioUni;
          $("#iva_fila_" + i).val(parseFloat(precioUni) * $('#impuestoIva').val());
          $("#iva_fila_sin_" + i).val(parseFloat(ivasin) * $('#impuestoIva').val());
          $("#baseImponible_" + i).val(
            Number(v.value) * Number($("#cantidad_" + i).val())
          );
        } else {
          subtotal0 += precioUni;
        }
      }
      else {
        subtotal0 += precioUni;
      }
      baseImponible += Number(v.value) * Number($("#cantidad_" + i).val());
      $("#total_" + i).val(precioUni.toFixed(2));
      precio += Number(v.value);
    });
  } catch (error) {
    console.log(error);
  }
  var total = subtotal0 + subtotal12 - descuento;
  var subtotal0 = parseFloat(subtotal0);
  var subtotal12 = parseFloat(subtotal12);
  iva = parseFloat(subtotal12) * $('#impuestoIva').val();
  baseImponibleTotal = parseFloat(baseImponible) + parseFloat(sumaivasin * $('#impuestoIva').val());
  var total_pagar = parseFloat(subtotal0) + parseFloat(subtotal12) + parseFloat(iva) - parseFloat(descuento);
  $("#subtotal_12").val(subtotal12.toFixed(2));
  $("#subtotal_0").val(subtotal0.toFixed(2));
  $("#descuento").val(descuento.toFixed(2));
  $("#iva").val(iva.toFixed(2));
  $("#total").val(total.toFixed(2));
  $("#total_pagar").val(total_pagar.toFixed(2));
}
function eliminarFila(fila) {
  $("#tr_" + fila).remove();
  fila--;
  calcular();
}
const validarProducto = () => {
  let msj = "";
  let validate = false;
  let selectProductos = document.querySelectorAll(".select2_producto");
  let btn_guardar = document.getElementById('btnguardarFactura');
  if (selectProductos.length <= 0) {
    mensajes('error', 'El documento debe tener al menos un producto');
    $('#btnguardarFactura').prop("disabled", false);
    btn_guardar.style.display = 'initial'
    validate = true;
  } else {
    for (let i = 0; i < selectProductos.length; i++) {
      let td = selectProductos[i].parentElement;
      if (selectProductos[i].value == "") {
        td.children[2].style.border = '1px solid red';
        msj = "No ha seleccionado producto";
        mensajes('error', msj);
        $('#btnguardarFactura').prop("disabled", false);
        btn_guardar.style.display = 'initial'
        validate = true;
      }
    }
  }
  return validate;
}
function asignarProducto(id) {
  $("#cantidad_" + id).focus();
  calcular();
}
function getPorcent(row) {
  let descuento = $('#descuento_' + row).val();
  let punitario = $('#precio_' + row).val();
  let cantidad = $('#cantidad_' + row).val();
  if (punitario > 0) {
    total = punitario * cantidad;
    let convert = (descuento * 100) / total;
    $('#pdescuento_' + row).val(convert);
    calcular();
  }
}
function getValue(row) {
  let pdescuento = $('#pdescuento_' + row).val();
  let punitario = $('#precio_' + row).val();
  let cantidad = $('#cantidad_' + row).val();
  if (punitario > 0) {
    total = punitario * cantidad;
    let convert = pdescuento * total / 100;
    $('#descuento_' + row).val(convert);
    calcular();
  }
}
function agregarImportacion() {
  window.open($("#base_url").val() + "/v3/importaciones/create");
}
function eliminarFila(fila) {
  $("#tr_" + fila).remove();
  window.fila--;
  calcular();
}


function abrirModalProveedor() {
  // limpiarModal();
  $("#id").val("");
  $('#logoProveedor').html('<i class="fas fa-user-tie fa-10x" style="color:#91B2F3;"></i>');
  $("#lblRuc").html("Ruc");
  $("#lblCedula").html("Cedula");
  $("#lblVotacion").html("Certificado de votación");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modalProveedor").html('<i class="fa-solid fa-user-tie"></i> ' + $("#nuevo").val() + " Proveedor");
  $("#modal_proveedores").modal("toggle");
}


$("#btnGuardarProveedor").click(function () {
  if ($("#tipo").val() == "") {
    return mensajes("warning", $("#alertTipoIdentificacion").val() + "...");
  } else if ($("#modal_proveedores #id").val() == "") {
    return mensajes("warning", $("#alertNumeroIdentificacion").val() + "...");
  } else if ($("#modal_proveedores #razonsocial").val() == "") {
    return mensajes("warning", $("#alertRazonSoc").val() + "...");
  } else if ($("#modal_proveedores #nombrecomercial").val() == "") {
    return mensajes("warning", $("#alertNomComercial").val() + "...");
  } else if ($("#modal_proveedores #email").val() == "") {
    return mensajes("warning", $("#alertCorreo").val() + "...");
  } else if ($("#modal_proveedores #telefono1").val() == "") {
    return mensajes("warning", $("#alertConvencional").val() + "...");
  } else if ($("#modal_proveedores #telefono2").val() == "") {
    return mensajes("warning", $("#alertCelular").val() + "...");
  } else if ($("#modal_proveedores #id_cuentas").val() == "") {
    return mensajes("warning", "Debe seleccionar una cuenta contable" + "...");
  } else if ($("#modal_proveedores #id_tipoproveedor").val() == "") {
    return mensajes("warning", "Debe seleccionar un tipo proveedor" + "...");
  } else if ($("#modal_proveedores #pais").val() == "") {
    return mensajes("warning", "Debe ingresar el país" + "...");
  } else if ($("#modal_proveedores #direccion").val() == "") {
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
    mensajes("question", $("#pregunta").val(), 'provee_frm', "actionSaveProveedor", "", "", "", "");
    $("#modal_proveedores").modal('hide');

  }
});



$("#proveedorLogo").on("change", function (e) {
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
