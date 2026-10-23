var filaPrecio = 0;
var filaProducto = 0;
var filaEquipo = 0;
var filaProcedimiento = 0;
var filaPaquete = 0;
$(document).ready(function () {
  $("#elementTour").val(
    "hero-factura,hero-datosfactura,hero-cliente,hero-paciente,hero-seguro,hero-agregarlinea,hero-agregarProducto"
  );
  try {
    $("#tableProductos").DataTable({
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
          window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "codigo",
        },
        {
          data: "nombre",
        },
        {
          data: "descripcion",
        },
        {
          className: "text-center",
          data: "stock_minimo",
        },
        {
          className: "text-right",
          data: "codigo",
        },
        {
          data: "codigo",
        },
        {
          data: "marca",
        },
        {
          data: "estado_tabla",
          render: function (data, type, row) {
            var estado = row.estado_tabla == 1 ? '<label class="text-success">' + $('#activo').val() + '</label>' : '<label class="text-danger">' + $('#inactivo').val() + '</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "codigo",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado_tabla == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProducto(\'' + row.codigo + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><a class="dropdown-item" href="javascript:;" onclick="verMovimiento(\'' + row.codigo + '\');"><i class="fas fa-people-carry" style="color:blue;"></i> Movimiento</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });

    $("#tableListadoDocumentoBodega").DataTable({
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
        { extend: "copy", text: '<i class="fa fa-copy" title="Copy"></i>' },
        { extend: "excel", title: tituloExcel, text: '<i class="fas fa-file-excel" title="Excel"></i>' },
        { extend: "pdf", title: tituloExcel, text: '<i class="fas fa-file-pdf" title="PDF"></i>' },
        {
          extend: "print",
          text: '<i class="fa fa-print" title="Print"></i>',
          customize: function (win) {
            $(win.document.body).addClass("white-bg").css("font-size", "10px");
            $(win.document.body).find("table").addClass("compact").css("font-size", "inherit");
          },
        },
      ],
      ajax: {
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
        type: "post",
        headers: { "X-CSRF-TOKEN": $("#token").val() },
        error(error) { console.log(error); },
      },
      columns: [
        { className: "text-right", data: "id" },
        {
          className: "text-center",
          data: "documento"
        },
        {
          className: "text-center",
          data: "abreviatura_documento"
        },
        {
          data: "id_inv_tipo_movimiento",
          render: function (data, type, row) {
            var estado = row.id_inv_tipo_movimiento == 1 ? '<label class="text-success">' + $('#ingreso').val() + '</label>' : '<label class="text-danger">' + $('#egreso').val() + '</label>';
            return estado;
          },
        },
        { // Acciones
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var botones =
              '<div class="btn-group hero-accion">'
              + '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button>'
              + '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown">'
              + '<span class="sr-only">Toggle Dropdown</span>'
              + '</button>'
              + '<div class="dropdown-menu" role="menu">'
              + '<a class="dropdown-item" href="javascript:;" onclick="verDocumentoBodega(\'' + row.id + '\');"><i class="fas fa-eye" style="color: green;"></i> ' + $("#ver").val() + '</a>'
              + '</div>'
              + '</div>';
            return botones;
          },
        },
      ],
    });
  } catch (error) {
    console.log(error);
  }
  $("#cta_gastos").select2({
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
  $("#cta_ventas").select2({
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
  $("#cta_costos").select2({
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
  $("#cta_devolucion").select2({
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
  $("#impuesto_iva_compras").select2({
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
  $("#impuesto_iva_ventas").select2({
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
  $("#agregarPrecio").on("click", function () {
    var html = '';
    html = '<tr id="tr_precio_' + filaPrecio + '">';
    html += '<td style="width: 45%;"><input type="text" class="form-control" id="nivel_' + filaPrecio + '" name="nivel[]"/></td>';
    html += '<td style="width: 45%;"><input type="number" class="form-control" id="precio_' + filaPrecio + '" name="precio[]" placeholder="0.00"></td>';
    html += '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarPrecio(' + filaPrecio + ')\"></i></td>';
    html += '</tr>';
    filaPrecio++;
    $("#detalle_precio").before(html);
  });
  $("#agregarProducto").on("click", function () {
    var html = '';
    html = '<tr id="tr_producto_' + filaProducto + '">';
    html += '<td style="width: 90%;"><select class="form-control select2_insumos" style="width:100%;" id="id_insumo_' + filaProducto + '" name="id_insumo[]"><option value="">--Seleccione--</option></select></td>';
    html += '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarProducto(' + filaProducto + ')\"></i></td>';
    html += '</tr>';
    filaProducto++;
    $("#productos").before(html);
    $(".select2_insumos").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      placeholder: "--" + $("#seleccione").val() + "--",
      minimumInputLength: 3,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getInsumoCombo",
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
  });
  $("#agregarEquipo").on("click", function () {
    var html = '';
    html = '<tr id="tr_equipo_' + filaEquipo + '">';
    html += '<td style="width: 90%;"><select class="form-control select2_equipos" style="width:100%;" id="id_equipo_' + filaEquipo + '" name="id_equipo[]"><option value="">--Seleccione--</option></select></td>';
    html += '<td align="center"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarEquipo(' + filaEquipo + ')\"></i></td>';
    html += '</tr>';
    filaEquipo++;
    $("#equipos").before(html);
    $(".select2_equipos").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      placeholder: "--" + $("#seleccione").val() + "--",
      minimumInputLength: 3,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getEquipoCombo",
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
  });
  $("#agregarProcedimientos").on("click", function () {
    var html = '';
    html = '<tr id="tr_procedimiento_' + filaProcedimiento + '">';
    html += '<td  style="width: 40%;"><select class="form-control select2_procedimientos" style="width:100%;" id="id_procedimiento_' + filaProcedimiento + '" name="id_procedimiento[]"><option value="">--Seleccione--</option></select></td>';
    html += '<td><select class="form-control select2_seguro" style="width:100%;" id="id_seguro_' + filaProcedimiento + '" name="id_seguro[]"><option value="">--Seleccione--</option></select></td>';
    html += '<td><input type="number" class="form-control" id="precio_' + filaProcedimiento + '" name="precio[]" placeholder="0.00"></td>';
    html += '<td align="center" style="width: 40px;"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarProcedimiento(' + filaProcedimiento + ')\"></i></td>';
    html += '</tr>';
    filaProcedimiento++;
    $("#procedimientos").before(html);
    $(".select2_seguro").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      placeholder: "--" + $("#seleccione").val() + "--",
      minimumInputLength: 3,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getSegurosCombo",
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
    $(".select2_procedimientos").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      placeholder: "--" + $("#seleccione").val() + "--",
      minimumInputLength: 3,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getProcedimientoCombo",
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
  });

  $("#agregarPaquete").on("click", function () {
    var html = '';
    html = '<tr id="tr_procedimiento_' + filaPaquete + '">';
    html += '<td><input type="number" class="form-control" id="cantidad_' + filaPaquete + '" name="cantidad[]" placeholder="0"></td>';
    html += '<td style="width: 50%;"><select class="form-control select2_paquete" style="width:100%;" id="id_paquete_' + filaPaquete + '" name="id_paquete[]"><option value="">--Seleccione--</option></select></td>';
    html += '<td><input type="number" class="form-control" id="precio_' + filaPaquete + '" name="precio[]" placeholder="0.00"></td>';
    html += '<td align="center" style="width: 40px;"><i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarPaquete(' + filaPaquete + ')\"></i></td>';
    html += '</tr>';
    filaPaquete++;
    $("#detallePaquetes").before(html);
    $(".select2_paquete").select2({
      noResults: function () {
        return "No hay resultado";
      },
      searching: function () {
        return "Buscando..";
      },
      placeholder: "--" + $("#seleccione").val() + "--",
      minimumInputLength: 3,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getPaquetesCombo",
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
  });

  $("#cta_debe").select2({
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
  $("#cta_haber").select2({
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
  $('#myModal').on('hidden.bs.modal', function () {
    $('#chkAsientoDocumentos').prop('checked', false);
    $('#tieneDetalle').val(0);
    toggleCuentasDocumentos();
  });
  $('#myModal').on('show.bs.modal', function () {
    const id = $('#id').val();
    const tieneDetalle = parseInt($('#tieneDetalle').val() || '0', 10);

    if (!id) {
      $('#chkAsientoDocumentos').prop('checked', false);
    } else {
      $('#chkAsientoDocumentos').prop('checked', tieneDetalle > 0);
      $('#chkAsientoDocumentos').prop('disabled', tieneDetalle > 0);
    }
    toggleCuentasDocumentos();
  });
  $('#myModalDocumentos').on('shown.bs.modal', toggleCuentasDocumentos);
  $(toggleCuentasDocumentos);
  $("#btnguardarDocumento").on("click", function () {
    const valForm = validarFormDocumentos();
    if (valForm.length == 0) {
      this.disabled = true;
      mensajes("question", $("#pregunta").val(), "frm_", "action", "");
      $('.tablaDatos').DataTable().ajax.reload();
    } else {
      mensajes("warning", formatearErroresDocumentos(valForm));
    }
  });
  const validarFormDocumentos = () => {
    const faltan = [];
    
      // --- Cabecera (todos obligatorios)
      if (!$('#documento').val())               faltan.push('Nombre');
      if (!$('#abreviatura_documento').val())              faltan.push('Abreviatura');
      if (!$('#id_inv_tipo_movimiento').val())             faltan.push('Tipo');
    
      // Bodega Recibe (hidden JSON o select)
      let bodOk = false;
      if ($('#bodega_principal').length) {
        try {
          const bod = JSON.parse($('#bodega_principal').val() || '{}');
          bodOk = !!(bod && bod.id);
        } catch (e) { bodOk = false; }
      } else if ($('#id_bodega').length) {
        bodOk = !!$('#id_bodega').val();
      }
      if ($('#chkAsientoDocumentos').is(':checked')) {
        const debe  = ($('#cta_debe').val()  || '').trim();
        const haber = ($('#cta_haber').val() || '').trim();
        if (!debe)  faltan.push('Cuenta Debe');
        if (!haber) faltan.push('Cuenta Haber');
      }
    
      return faltan;
  };
  const formatearErroresDocumentos = (arr) =>
    "Ingrese información del campo:\n" + arr.map((e,i) => `• ${e}`).join("\n");
});
function agregarDocumento() {
  limpiarModal();
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i> ' + $("#nuevo").val() + " " + $("#submodulo_").val());
  $("#myModal").modal("toggle");
}
function eliminarPrecio(filaPrecio) {
  $("#tr_precio_" + filaPrecio).remove();
  filaPrecio--;
}
function eliminarProducto(filaProducto) {
  $("#tr_producto_" + filaProducto).remove();
  filaProducto--;
}
function eliminarEquipo(filaEquipo) {
  $("#tr_equipo_" + filaEquipo).remove();
  filaEquipo--;
}
function eliminarProcedimiento(filaProcedimiento) {
  $("#tr_procedimiento_" + filaProcedimiento).remove();
  filaProcedimiento--;
}
function eliminarPaquete(filaPaquete) {
  $("#tr_paquete_" + filaPaquete).remove();
  filaPaquete--;
}
function verProducto(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getProductos",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#titulo_modal").html('<i class="fas fa-edit"></i> ' + $("#editar").val() + " producto " + json.nombre);
      $("#codigo").val(json.codigo);
      $("#nombre").val(json.nombre);
      $("#modelo").val(json.modelo);
      $("#codigo_barra").val(json.codigo_barra);
      $("#grupo").val(json.grupo).trigger("change");
      $("#marca").val(json.marca).trigger("change");
      $("#stock_minimo").val(json.stock_minimo);
      $("#fecha_expiracion").val(json.fecha_expiracion);
      //$("#proveedor").val(json.proveedor).trigger("change");
      $("#tipo_producto").val(json.tipo_producto).trigger("change");
      $("#reg_serie").val(json.reg_serie);
      $("#mod_desc").val(json.mod_desc);
      $("#mod_precio").val(json.mod_precio);
      console.log('iva: ' + json.iva);
      $("#iva").val(json.iva);
      $("#descripcion").val(json.descripcion);
      $("#cta_gastos").val(json.cta_gastos).trigger("change");
      $("#cta_ventas").val(json.cta_ventas).trigger("change");
      $("#cta_costos").val(json.cta_costos).trigger("change");
      $("#cta_devolucion").val(json.cta_devolucion).trigger("change");
      $("#impuesto_iva_compras").val(json.impuesto_iva_compras).trigger("change");
      $("#impuesto_iva_ventas").val(json.impuesto_iva_ventas).trigger("change");
      $("#impuesto_servicio").val(json.impuesto_servicio).trigger("change");
      $("#impuesto_ice").val(json.impuesto_servicio).trigger("change");
      $("#clasificacion_impuesto_ice").val(json.clasificacion_impuesto_ice).trigger("change");
      $("#descuento").val(json.descuento);
      $("#financiero").val(json.financiero);
      $("#promedio").val(json.promedio);
      $("#reposicion").val(json.reposicion);
      $("#lista").val(json.lista);
      $("#ultima_compra").val(json.ultima_compra);

      $.ajax({
        url: "v3/" + $("#controlador").val() + "/buscarProveedor",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.proveedor,
        },
        dataType: "html",
        success: function (html) {
          $("#proveedor").append(html).trigger("change");
        },
      });

      $.ajax({
        url: "v3/" + $("#controlador").val() + "/getAllCuentas",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.cta_gastos,
        },
        dataType: "html",
        success: function (html) {
          $("#cta_gastos").append(html).trigger("change");
        },
      });

      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }

      $("#myModal").modal("toggle");
    },
    error(error) {
      console.log(error);
    },
  });
}

$(document).on('change', '#chkAsientoDocumentos', toggleCuentasDocumentos);

function verMovimiento(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getMovimientos",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      console.log(json.movimientos);
      console.log(json.html);
      $("#titulo_modal_movimiento").html('<i class="fas fa-edit"></i> ' + $("#editar").val() + " producto " + json.movimientos[0].nombre_contable);
      $('#bodyMovimientos').before(json.html);
      $("#modalMovimiento").modal("toggle");
    },
    error(error) {
      console.log(error);
    },
  });
}

function agregarDocumentoBodegas() {
  limpiarModal();
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modalDocumentos").html('<i class="fa-solid fa-user-tie"></i> ' + $("#nuevo").val() + " Documento Bodega");
  $("#myModal").modal("toggle");
}
function verDocumentoBodega(id) {
  limpiarModal();
  $("#titulo_modalDocumentos").html(
    '<i class="fas fa-edit"></i> ' + ($('#tituloNuevo')?.val() || 'Editar ' + $("#submodulo").val())
  );

  $.ajax({
    url: "v3/" + $("#controlador").val() + "/verDocumentoBodega",
    type: "post",
    headers: { "X-CSRF-TOKEN": $("#token").val() },
    data: { id: id },
    dataType: "json",
    success: function (json) {
      if (json && json.ok === false) { console.error(json.msg || 'Error'); return; }

      // set valores base
      $("#id").val(json.id || '');
      $("#documento").val(json.documento || '');
      $("#abreviatura_documento").val(json.abreviatura_documento || '');
      $("#id_inv_tipo_movimiento").val(json.id_inv_tipo_movimiento).trigger('change');

      var isNew   = !(json && json.id);
      var checked = isNew ? false : ((parseInt(json.tieneDetalle || 0, 10) > 0) || (json.genera_asiento == 1));
      $("#chkAsientoDocumentos").prop('checked', checked);

      // Preselección cuentas
      if (json && json.cta_debe && json.cta_debe.id) {
        var $debe = $("#cta_debe");
        if ($debe.find('option[value="' + json.cta_debe.id + '"]').length === 0) {
          $debe.append(new Option(json.cta_debe.text || json.cta_debe.id, json.cta_debe.id, true, true));
        }
        $debe.val(json.cta_debe.id).trigger('change');
      } else {
        $("#cta_debe").val('').trigger('change');
      }

      if (json && json.cta_haber && json.cta_haber.id) {
        var $haber = $("#cta_haber");
        if ($haber.find('option[value="' + json.cta_haber.id + '"]').length === 0) {
          $haber.append(new Option(json.cta_haber.text || json.cta_haber.id, json.cta_haber.id, true, true));
        }
        $haber.val(json.cta_haber.id).trigger('change');
      } else {
        $("#cta_haber").val('').trigger('change');
      }

      $("#tieneDetalle").val(json.tieneDetalle || 0);

      // --- ESTADO FINAL ---
      // 1) Primero aplica visibilidad de cuentas según el check
      if (typeof toggleCuentasDocumentos === 'function') { toggleCuentasDocumentos(); }

      // 2) Luego establece bloqueo/edición definitivo
      if (isNew) {
        // NUEVO: todo habilitado (las cuentas quedarán deshabilitadas solo si el check está apagado)
        $('#documento').prop('disabled', false).prop('readonly', false);
        $('#abreviatura_documento').prop('disabled', false).prop('readonly', false);
        $('#id_inv_tipo_movimiento').prop('disabled', false).trigger('change.select2');
        $('#chkAsientoDocumentos').prop('disabled', false);
        $('#cta_debe, #cta_haber').prop('disabled', false).trigger('change.select2');
      } else {
        $('#documento').prop('disabled', false).prop('readonly', false);
        $('#abreviatura_documento').prop('disabled', true);
        $('#id_inv_tipo_movimiento').prop('disabled', true).trigger('change.select2');
        $('#chkAsientoDocumentos').prop('disabled', true);
        $('#cta_debe, #cta_haber').prop('disabled', true).trigger('change.select2');
      }

      $("#myModal").modal("show");
    },
    error() {
      // window.open("v3/admin", "_self");
    },
  });
}
function toggleCuentasDocumentos() {
  const checked = $('#chkAsientoDocumentos').is(':checked');
  const isEdit  = !!$('#id').val();
  $('#bloqueCuentas').toggleClass('d-none', !checked);

  // si es edición, mantenerlos siempre deshabilitados
  const shouldDisable = isEdit ? true : !checked;
  $('#bloqueCuentas select').prop('disabled', shouldDisable).trigger('change.select2');

  if (!checked) $('#cta_debe, #cta_haber').val('').trigger('change');
}
function getTieneDetalle(id){
  return $.ajax({
    url: "v3/" + $("#controlador").val() + "/tieneDetalle",
    type: "post",
    headers: { "X-CSRF-TOKEN": $("#token").val() },
    data: { id: id },
    dataType: "json",
    success: function (json) {
      $("#tieneDetalle").val(json > 0 ? 1 : 0);
      console.log(json);
      document.getElementById('chkAsientoDocumentos').checked = json > 0;
    },
    error() {
      window.open("v3/admin", "_self");
    },
  });
}

