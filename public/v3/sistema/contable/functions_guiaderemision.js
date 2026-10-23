var cantidadFila = 0;
$(document).ready(function () {
  $(".select2_tipoDoc").select2({
    tags: false,
  });

  $(".select2_cuentas").select2({
    tags: false,
  });

  let refresh = $("#sucursal_empresa").val();

  if (refresh != null) {
    $("#sucursal_empresa").trigger("change");
  }

  try {
    $("#tableTransportistas").DataTable({
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
        url: "v3/" + $('#controlador').val() + "/" + $('#submodulo_').val() + "js",
        type: 'post',
        headers: {
          'X-CSRF-TOKEN': $('#token').val(),
        }, error(error) {
          console.log(error);

        }
      },
      columns: [{
        className: 'text-center',
        data: 'ci_ruc'
      }, {
        data: 'razon_social'
      },
      {
        data: 'nombrecomercial'
      }, {
        className: 'text-center',
        data: 'ciudad'
      }, {
        className: 'text-center',
        data: 'tipo'
      }, {
        className: 'text-center',
        data: 'rise',
        render: function (data, type, row) {
          var rise =
            row.rise == 1
              ? '<label>Si</label>'
              : '<label>No</label>';
          return rise;
        },
      }, {
        className: 'text-center',
        data: 'contabilidad',
        render: function (data, type, row) {
          var contabilidad =
            row.contabilidad == 1
              ? '<label>Si</label>'
              : '<label>No</label>';
          return contabilidad;
        },
      }, {
        className: 'text-center',
        data: 'contribuyente_especial',
        render: function (data, type, row) {
          var contribuyente_especial =
            row.contribuyente_especial == 1
              ? '<label>Si</label>'
              : '<label>No</label>';
          return contribuyente_especial;
        },
      }, {
        className: 'text-center',
        data: 'estado',
        render: function (data, type, row) {
          var estado = row.estado == 1 ? '<label class="text-success">'+$('#activo').val()+'</label>' : '<label class="text-danger">'+$('#inactivo').val()+'</label>';
          return estado;
        },
      }, {
        className: 'text-center',
        data: 'id',
        render: function (data, type, row) {
          var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
          if (row.estado == 1) {
            accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
          }
          var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verTransportista(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Transportista');\">" + accion + "</a></div></div>";
          return botones;
        }
      }],
    });
    $("#tableListadeguias").DataTable({
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
          className: "text-right",
          data: "id",
        },
        {
          className: "text-center",
          data: "num_secuencial",
          render: function (data, type, row) {
            return row.establecimiento + " - " + row.punto_emision + " - " + PadLeft(row.num_secuencial, 9);
          },
        },
        {
          data: "razon_social_destinatario",
        },
        {
          className: "text-center",
          data: "fecha_emision_documento",
          render: function (data, type, row) {
            if (row.fecha_emision_documento !== null) {
              return getfecha(row.fecha_emision_documento, null, "-");
            } else {
              return "";
            }
          },
        },
        {
          data: "razon_social",
        },
        {
          data: "placa",
        },
        {
          data: "direccion_partida",
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            let estado = '';
            if (row.estado == -1) {
              estado += '<label class="text-success">'+$('#activo').val()+'</label>';
            }
            if (row.estado == 0) {
              estado += '<label class="text-warning">'+$('#enproceso').val()+'</label>';
            }
            if (row.estado == 7) {
              estado += '<label class="text-danger">'+$('#error').val()+'</label>';
            }
            if (row.estado == 9) {
              estado += '<label class="text-danger">'+$('#error').val()+'</label>';
            }
            if (row.estado == 5) {
              estado += '<label class="text-primary">'+$('#aprobada').val()+'</label>';
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
            var botones = '<div class="btn-group hero-accion">' + '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button>' + '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown">' + '<span class="sr-only">Toggle Dropdown</span>' + "</button>" + '<div class="dropdown-menu" role="menu">' + '<a class="dropdown-item" href="javascript:;" onclick="editarGuia(\'' + row.id + '\');"><i class="fa fa-pencil-square-o"></i>Editar</a>' + '<div class="dropdown-divider"></div>' + '<a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "', '" + row.estado + "', 'Transportista');\">" + accion + "</a>" + '<a class="dropdown-item" href="javascript:;" onclick="verGuiaRemision(\'' + row.id + '\');"><i class="fas fa-eye" style="color: green;"></i> ' + $("#ver").val() + "</a>" + "</div>" + "</div>";
            return botones;
          },
        },
      ],
    });
  } catch (error) {
    console.log(error);
  }

  if ($("#id").val() != "") {
    $("#contabilidad").val(0).trigger("change");
    $("#contribuyente_especial").val(0).trigger("change");
    $("#rise").val(0).trigger("change");
  }
  $("#btnGuardarTransportista").click(function () {
    if ($("#ci_ruc").val() == "") {
      mensajes("info", "Ingrese el ruc");
    } else if ($("#razon_social").val() == "") {
      mensajes("info", "Ingrese la razon social");
    } else if ($("#tipo_documento").val() == "") {
      mensajes("info", "Seleccione el tipo documento");
    } else if ($("#nombrecomercial").val() == "") {
      mensajes("info", "Ingrese el nombre comercial");
    } else if ($("#pais").val() == "") {
      mensajes("info", "Ingrese el pais");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#id_ciudad").change(function () {
    if ($("#id_ciudad").val() != "") {
      $("#ciudad").val($('select[name="id_ciudad"] option:selected').text());
    }
  });

  $(".js-data-nombre-ruc")
    .select2({
      minimumInputLength: 3,
      cache: true,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/getTransportistaDatosGuia",
        delay: 250,
        data: function (params) {
          return {
            term: params.term,
          };
        },
        processResults: function (data) {
          if (data.length == 0) {
            aparecerElemento();
          } else {
            esconderElemento();
          }
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
          return "Por favor ingrese 3 caracteres....";
        },
      },
    })
    .on("select2:select", function (e) {
      console.log("dasdasdasd");
      var selectedOption = e.params.data;
      $.ajax({
        type: "post",
        url: "v3/" + $("#controlador").val() + "/llenarCamposTransportista",
        headers: {
          "X-CSRF-TOKEN": $("input[name=_token]").val(),
        },
        datatype: "json",
        data: {
          id: this.value,
          tipo: selectedOption.tipo,
        },
        success: function (soli) {
          console.log(soli);
          let data = soli.data;
          let cedula = data.identificacion;
          let correo = data.email_representante;
          let direccion = data.direccion_representante;
          let razon_social = data.razonsocial;
          let telefono = data.telefono1;
          let placa = data.placa;
          $("#frm_guia_remision, #ci_ruc").val(cedula);
          $("#frm_guia_remision, #email_transportista").val(correo);
          $("#frm_guia_remision, #razon_social").val(razon_social);
          $("#frm_guia_remision, #telefono1").val(telefono);
          $("#frm_guia_remision, #placa").val(placa);
        },
        error: function (data) {
          console.log(data);
        },
      });
    });
  $(".select2_no_docs")
    .select2({
      minimumInputLength: 3,
      cache: true,
      ajax: {
        url: "v3/" + $("#controlador").val() + "/traerFacturas",
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
                sad: item.autorizacion,
                correo: item.email,
                direccion_representante: item.direccion_representante,
                cedula_destinatario: item.id_cliente,
                nombre: item.nombre,
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
    })
    .on("select2:select", function (e) {
      let selectedOption = e.params.data;
      console.log(selectedOption);
      $("#codigo_esta_destino").val("001");
      let arr = selectedOption.text
        .split("-")
        .map((val, i) => {
          if (i == 0) {
            if (val.length != 3) {
              alertas("error", "Error", ["Erron en formato 000-000-000000000"]);
            }
          } else if (i == 1) {
            if (val.length != 3) {
              alertas("error", "Error", ["Erron en formato 000-000-000000000"]);
            }
          } else if (i == 2) {
            if (val.length != 9) {
              let vao = PadLeft(parseInt(val), 9);
              return vao.length == 9
                ? PadLeft(parseInt(val), 9)
                : alertas("error", "Error", [
                  "Erron en formato 000-000-000000000",
                ]);
            }
          }
          return val;
        })
        .join("-");
      let correo = selectedOption.correo;
      let direccion_representante = selectedOption.direccion_representante;
      let cedula_destinatario = selectedOption.cedula_destinatario;
      let nombre = selectedOption.nombre;

      $("#email_traslado_destinatario").val(correo);
      $("#direccion_destinatario").val(direccion_representante);
      $("#ci_destinatario").val(cedula_destinatario);
      $("#razon_social_destinatario").val(nombre);
      limpiar();
      handleChange(arr);
    });
});

function editarGuia(id_Guia) {
  window.open($("#base_url").val() + "/v3/guiaderemision/crearGuia?id=" + id_Guia, "_self");
}

function agregarTransportista() {
  limpiarModal();
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fas fa-plus-square"></i> Nuevo Transportista'
  );
  $("#myModal").modal("toggle");
}

function verTransportista(id) {
  var data = {id:id};
  var json = ajax("v3/" + $("#controlador").val() + "/getTransportista",data,'json');
  json = json.responseJSON;
  if (json != null)
  {
    $("#referencia").val(json.referencia);
    $("#urbanizacion").val(json.urbanizacion);
    $("#etapa").val(json.etapa);
    $("#manzana").val(json.manzana);
    $("#villa_solar").val(json.villa_solar);
    $("#direccion").val(json.direccion);
    $("#web").val(json.web);
    $("#email").val(json.email);
    $("#placa").val(json.email);
    $("#telefono1").val(json.telefono1);
    $("#telefono2").val(json.telefono2);
    $("#id").val(json.id);
    $("#ci_ruc").val(json.ci_ruc);
    $("#razon_social").val(json.razon_social);
    $("#nombrecomercial").val(json.nombrecomercial);
    $("#ciudad").val(json.ciudad);
    $("#tipo_documento").val(json.tipo).trigger("change");
    $("#rise").val(json.rise).trigger("change");
    $("#contabilidad").val(json.contabilidad).trigger("change");
    $("#contribuyente_especial")
      .val(json.contribuyente_especial)
      .trigger("change");
    $("#titulo_modal").html(
      '<i class="fas fa-plus-square"></i> Modificar Configuración'
    );
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
    $("#myModal").modal("show");

  }
 
}

$("#sucursal_empresa").on("change", function () {
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

$("#btnagregarAdicional").on("click", function () {
  let fila = `<tr id="fila${cantidadFila}">
  
                  <td >
                      <select style="width: 100%" id="producto${cantidadFila}" name="producto[]" class="form-control  validaciones nuevo_producto select2_cuentas nueva_vali_producto" require onchange="cargar_codigos(this,${cantidadFila})" >
                      </select>
                  </td>
                  <td>
                      <input  id="descripcion${cantidadFila}"  class="form-control text-right nueva_vali_descrip" type="text"  name="descripcion[]" required >
                  </td>
                  <td>
                      <input  id="observacion${cantidadFila}"  class="form-control text-right " type="text"  name="observacion[]"  required >
                  </td>
                  <td>
                      <input  id="detalle3${cantidadFila}"  class="form-control text-right " type="text"  name="detalle3[]">
                  </td>
                  <td>
                      <input  id="cantidad${cantidadFila}"  class="form-control text-right " type="number"  value="1" name="cantidad[]" onchange="validateZ(this);" onBlur="cantidadVathis(this)" required >
                  </td>
                  <td>
                      <input readonly id="cod_principal${cantidadFila}" type="text" class="form-control validar" name="cod_principal[]"  required >
                  </td>
                  <td>
                      <input readonly id="cod_adicional${cantidadFila}" class="form-control px-1 text-right precioneto" type="text" name="cod_adicional[]" required >
                  </td>
                 
                  <td>
                     <i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(${cantidadFila})" aria-hidden="true"></i>
                  </td>
            </tr>
          `;

  $("#detalleFactura").append(fila);
  $(".select2_cuentas").select2({
    tags: false,
  });
  cantidadFila += 1;
  activate();
});

function eliminarFila(id) {
  cantidadFila -= 1;
  $("#fila" + id).remove();
}

$("#btneliminar").on("click", function () {
  $("#fila" + cantidadFila).remove();
  cantidadFila -= 1;
});

function aparecerElemento() {
  $("#btnaregartransportista").show();
}
function esconderElemento() {
  $("#btnaregartransportista").hide();
}

$("#btnaregartransportista").on("click", function () {
  $("#modal_transportista").modal("toggle");
});

$("#tipo_documento").on("change", function () {
  let tipo_documento = $("#tipo_documento").val();
  let ruc_ced = $("#ci_ruc").val();
  if (tipo_documento == "05") {
    let validar = ruc_ced.length;

    if (validar !== 10) {
      mensajes("error", "El formato de la cédula no es correcto");
      $("#tipo_documento").val("");
    }
  } else if (tipo_documento == "04") {
    let validar = ruc_ced.length;

    if (validar !== 13) {
      mensajes("error", "El formato del ruc no es correcto");
      $("#tipo_documento").val("");
    }
  }
});

$("#btntransportistaNuevo").on("click", function () {
  if ($("#ci_ruc").val() == "") {
    console.log("entre 2");
    return mensajes("warning", "Inserte la cédula del transportista...");
  } else if ($("#email").val() == "") {
    return mensajes("warning", "Inserte el correo del transportista...");
  } else if ($("#frm_modaltransportista #placa").val() == "") {
    return mensajes("warning", "Inserte la placa del transportista...");
  } else if ($("#direccion").val() == "") {
    return mensajes("warning", "Inserte la dirección del transportista...");
  } else if ($("#razon_social").val() == "") {
    return mensajes("warning", "Inserte la razon social del transportista...");
  } else if ($("#telefono1").val() == "") {
    return mensajes("warning", "Inserte el telefono del transportista...");
  } else if ($("#tipo_documento").val() == "") {
    return mensajes(
      "warning",
      "Inserte el tipo documento del transportista..."
    );
  } else {
    mensajes(
      "question",
      $("#pregunta").val(),
      "frm_modaltransportista",
      "actionSaveTransportista",
    );
  }
});

$("#ci_ruc").on("blur", function () {
  let numero = $(this).val();
  let resp = validarDocumento(numero);
  console.log(resp);
  if (resp) {
    $.ajax({
      type: "get",
      url: "v3/" + $("#controlador").val() + "/validarCampos",
      headers: {
        "X-CSRF-TOKEN": $("input[name=_token]").val(),
      },
      datatype: "json",
      data: {
        numero: numero,
      },
      success: function (data) {
        data = JSON.parse(data);
        console.log(data.status);
        if (!data.status) {
          $("#ci_ruc").val("");
          mensajes("error", data.message);
        }
      },
      error: function (data) {
        console.log(data);
      },
    });
  }
});

$("#frm_modaltransportista #placa").on("blur", function () {
  $.ajax({
    type: "get",
    url: "v3/" + $("#controlador").val() + "/validarPlaca",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      placa: $(this).val(),
    },
    success: function (data) {
      data = JSON.parse(data);
      console.log(data.status);
      if (!data.status) {
        $("#frm_modaltransportista #placaM").val("");
        mensajes("error", data.message);
      }
    },
    error: function (data) {
      console.log(data);
    },
  });
});

$("#email").on("blur", function () {
  let correo = $(this).val();
  let regexCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (!regexCorreo.test(correo)) {
    return mensajes("error", "El correo no tiene el formato correcto");
  }

  let partes = correo.split("@");
  if (partes.length !== 2 || partes[1].indexOf(".") === -1) {
    return mensajes("error", "El correo no tiene el formato correcto");
  }

  if (
    partes[0].startsWith(".") ||
    partes[0].endsWith(".") ||
    partes[1].startsWith(".") ||
    partes[1].endsWith(".")
  ) {
    return mensajes("error", "El correo no tiene el formato correcto");
  }
});

$("#tipo").on("change", function () {
  let valor = $(this).val();
  if (valor == 26) {
    $("#num_doc_destino").prop("disabled", false);
  } else {
    $("#num_doc_destino").prop("disabled", true);
  }
});

function activate() {
  let studentSelect = $(".nuevo_producto");
  $(".nuevo_producto").select2({
    tokenSeparators: [","],
    minimumInputLength: 2,
    cache: false,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/consultarProdu",
      delay: 250,
      data: function (params) {
        return {
          term: params.term,
        };
      },
      processResults: function (data, page) {
        return {
          results: $.map(data, function (item) {
            return {
              text: item.nombre,
              id: item.id + "|" + item.tipo,
            };
            var option = new Option(item.nombre, item.id, true, true);
            studentSelect.append(option).trigger("change");
          }),
        };
      },
    },
  });
}

const cargar_codigos = (value, id) => {
  console.log(value, id);
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/revisarCodigo",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      id_producto: value.value,
    },
    success: function (datos) {
      $("#cod_principal" + id).val(datos.producto.codigo);
      $("#cod_adicional" + id).val(datos.producto.codigo);
    },
    error: function (data) {
      console.log(data);
    },
  });
};

const validarForm = () => {
  let formulario = document.forms["frm_guia_remision"];

  let array = [];
  if (formulario.sucursal_empresa.value == "") {
    array.push("sucursal");
  } else if (formulario.punto_emision.value == "") {
    array.push("punto emisión");
  } else if (formulario.tipo_guia.value == "") {
    array.push("tipo guía");
  } else if (formulario.direccion_partida.value == "") {
    array.push("direccion de partida");
  } else if (formulario.placa.value == "") {
    array.push("placa");
  } else if (formulario.fecha_ini.value == "") {
    array.push("fecha Inicio");
  } else if (formulario.fecha_fin.value == "") {
    array.push("fecha Fin");
  } else if (formulario.ruta.value == "") {
    array.push("Ruta");
  } else if (formulario.ci_destinatario.value == "") {
    array.push("nombre ci del destinatario");
  } else if (formulario.direccion_destinatario.value == "") {
    array.push("dirección destino");
  } else if (formulario.email_traslado_destinatario.value == "") {
    array.push("email destinatario");
  } else if (formulario.tipo.value != "") {
    if (formulario.fecha_autorizacion_destinatario.value == "") {
      array.push("fecha autorización");
    }
    if (formulario.num_doc_destino.value == "") {
      array.push("fecha numero documento");
    }
  } else if (formulario.motivo_traslado_destinatario.value == "") {
    array.push("motivo traslado");
  } else if ($("#detalleFactura").children().length == 0) {
    array.push("Agregue un producto");
  } else {
    let contador = document.getElementsByClassName("nueva_vali_producto");
    let contador1 = document.getElementsByClassName("nueva_vali_descrip");

    for (let p = 0; p < contador.length; p++) {
      if (contador[p].value == "") {
        array.push("Agregue el producto en la fila " + p);
      }
    }

    for (let d = 0; d < contador1.length; d++) {
      if (contador1[d].value == "") {
        array.push("Agregue la descripción del producto en la fila " + d);
      }
    }
  }
  return array;
};

$("#btnguardarGuia").on("click", function () {
  let valForm = validarForm();
  if (valForm.length == 0) {
    document.getElementById("btnguardarGuia").disabled = true;
    mensajes("question", $("#pregunta").val(), "frm_guia_remision", "action", "v3/guiaderemision/listadeguias");
  } else {
    mensajes("warning", "Ingrese información del campo " + valForm.join(","));
  }
});

const validateZ = (val) => {
  if (val.value < 0) {
    $("#" + val.id).val(0);
  }
};

const cantidadVathis = (e) => {
  if (e.value == "" || e.value == 0) {
    mensajes("error", "La cantidad no puede ser vacia");
    e.value = 1;
  }
};

const validacioneFecha = () => {
  let fechaIni = new Date($("#fecha_ini").val());
  let fechaFin = new Date($("#fecha_fin").val());
  fechaIni.setDate(fechaIni.getDate() + 1);
  fechaFin.setDate(fechaFin.getDate() + 1);
  let fechaHoy = new Date();
  if (fechaIni <= fechaHoy) {
    mensajes("warning", "La fecha inicio no puede ser menor que el dia de hoy");
  }
  if (fechaFin < fechaIni) {
    mensajes("warning", "La fecha fin no pueser ser menor");
    $("#fecha_fin").val("");
  }
};

function verGuiaRemision(id) {
  var data={id_guia:id};
  var html=ajax("v3/" + $("#controlador").val() + "/getGuiaPrint",data,'html');
  html=html.responseText;
  $("#contenidoHtml").html(html);
  $("#modalTotal").modal("show");
}

const fecha_aut = (e) => {
  let fecha = new Date(e.value);
  fecha.setDate(fecha.getDate() + 1);
  let fechaHoy = new Date();
  let nuevaFecha = new Date(
    fecha.getFullYear(),
    fecha.getMonth(),
    fecha.getDate()
  );
  let nuevaFecha1 = new Date(
    fechaHoy.getFullYear(),
    fechaHoy.getMonth(),
    fechaHoy.getDate()
  );
  if (nuevaFecha > nuevaFecha1) {
    mensajes(
      "warning",
      "La fecha de autorización no puede ser mayor que el dia de hoy"
    );
    $("#fecha_autorizacion_destinatario").val("");
  }
};

const limpiar = () => {
  let contador = document.getElementById("fila0");

  if (contador != null) {
    contador = 0;
    $("#detalleFactura").empty();
  }
};

const crearFilaForEach = (data) => {
  let t;
  console.log(data);
  for (t = 0; t < data.length; t++) {
    let fila = `
      <tr id="fila${t}">
                      <td>
                      <select id="producto${t}" name="producto[]" class="form-control" style="width:220px;height:20px" required>
                          <option value="${data[t].id}|${data[t].tipo}">${data[t].nombre} | ${data[t].codigo}</option>
                      </select>
                      </td>
                      <td>
                          <input  id="descripcion${t}"  class="form-control text-right " type="text" name="descripcion[]" required >
                      </td>
                      <td>
                          <input  id="observacion${t}"  class="form-control text-right " type="text" name="observacion[]" >
                      </td>
                      <td>
                          <input  id="detalle3${t}"  class="form-control text-right " type="text"  name="detalle3[]" onchange="validateZ(this);">
                      </td>
                      <td>
                          <input  id="cantidad${t}"  class="form-control text-right " type="number" value="1" name="cantidad[]" onchange="validateZ(this);" onBlur="cantidadVathis(this)" required >
                      </td>
                      <td>
                          <input readonly id="cod_principal${t}" type="text" class="form-control validar" name="cod_principal[]" required >
                      </td>
                      <td>
                          <input readonly id="cod_adicional${t}" class="form-control px-1 text-right precioneto" type="text" name="cod_adicional[]" required >
                      </td>
                     
                      <td>
                      <i class="far fa-trash-alt" style="color:red;cursor:pointer;" onclick="eliminarFila(${t})" aria-hidden="true"></i>
                   </td>
      </tr>
`;
    $("#detalleFactura").append(fila);

    $(`#cod_principal${t}`).val(data[t]["id_ct_productos"]);
    $(`#cod_adicional${t}`).val(data[t]["id_ct_productos"]);
    $(`#producto${t}`)
      .select2()
      .val(data[t]["id"] + "|" + data[t]["tipo"])
      .trigger("change");
    $(`#tipo_objeto${t}`).val(data[t]["tipo"]);
    $(".select2_cuentas").select2({
      tags: false,
    });
  }
  cantidadFila = t;
};

const handleChange = (e) => {
  $.ajax({
    type: "post",
    url: "v3/" + $("#controlador").val() + "/crearTablaFactura",
    headers: {
      "X-CSRF-TOKEN": $("input[name=_token]").val(),
    },
    datatype: "json",
    data: {
      producto: e,
    },
    success: function (data) {
      console.log(data);
      if (data["query"].length > 0) {
        let fecha = data["query"][0]["created_at"];
        console.log(fecha);
        let fechaNew = fecha.split(" ");
        document.getElementById("fecha_autorizacion_destinatario").value =
          fechaNew[0];
      }

      crearFilaForEach(data["query"]);
    },
    error: function (data) { },
  });
};

$("#btneliminar").on("click", function () {
  history.back();
});



