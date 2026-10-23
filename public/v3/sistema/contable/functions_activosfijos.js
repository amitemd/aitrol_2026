$(document).ready(function () {
  if ($("#submodulo_").val() == "crearDepreciacion") {
    $(".botonMenu").trigger("click");
  }
  $("#elementTour").val("hero-contenido,hero-accion,hero-agregar");
  try {
    $("#tableListadeActivosFijos").DataTable({
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
          data: "fecha_compra",
          render: function (data, type, row) {
            if (row.fecha_compra !== null) {
              return getfecha(row.fecha_compra, null, "-");
            } else {
              return "";
            }
          },
        },
        {
          data: "codigo",
        },
        {
          data: "nombre",
        },
        {
          data: "tipo",
        },
        {
          data: "categoria",
        },
        {
          data: "responsable",
        },
        {
          data: "ubicacion",
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
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group hero-accion">'+
            '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button>'+
            '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button>'+
            '<div class="dropdown-menu" role="menu">'+
            '<a class="dropdown-item" href="javascript:;" onclick="verListadeActivos(\''+row.id+'\');"><i class="fas fa-eye" style="color:green;"></i> '+$("#ver").val()+'</a>'+
            '<a class="dropdown-item" href="javascript:;" onclick="descargarPDFActivo(\''+row.id+'\');"><i class="fa-solid fa-file-pdf" style="color:green;"></i> '+'PDF'+'</a>'+
            '<a class="dropdown-item" href="javascript:;" onclick="descargarBarcodePDFActivo(\''+row.id+'\');"><i class="fa-solid fa-barcode" style="color:green;"></i> '+'Barcode'+'</a>'+
            '<a class="dropdown-item" href="javascript:;" onclick="verListadeActivos(\''+row.id+'\');"><i class="fa-solid fa-file-excel" style="color:green;"></i> '+$("#depreciacion_acumulada").val()+'</a>'+
            '<div class="dropdown-divider"></div>'+
            '<a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','ActivosFijos');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableGruposActivosFijos").DataTable({
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
          data: "codigo",
        },
        {
          data: "grupo",
        },
        {
          className: "text-center",
          data: "cod_tipo",
        },
        {
          data: "nombre",
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado =
              row.estado == 1 ? '<label class="text-success">' + $("#activo").val() + "</label>" : '<label class="text-danger">' + $("#inactivo").val() + "</label>";
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
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verGruposActivosFijos(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a> <a class="dropdown-item" href="javascript:;" onclick="Subtipos(\'' + row.id + '\');"><i class="fas fa-plus-circle" style="color:blue;"></i> Subtipos </a> <div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','GrupoActivosFijos');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableSubtipos").DataTable({
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js?id=" + $("#id_tipo").val(),
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
          data: "codigo",
        },
        {
          data: "nombre",
        },
        {
          data: "tipo",
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
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verSubtipo(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','SubtipoAf');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableListaDepreciaciones").DataTable({
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
      responsive: false,
      lengthChange: false,
      autoWidth: true,
      info: false,
      paginate: false,
      iDisplayLength: 500,
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
    });
    $("#tableDepreciaciones").DataTable({
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
      paginate: true,
      responsive: true,
      lengthChange: true,
      autoWidth: true,
      pageLength: 50,
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
          console.log(error);
        },
      },
      columns: [
        {
          className: "text-center",
          data: "activo_id",
        },
        {
          data: "codigo",
        },
        {
          data: "nombre",
        },
        {
          className: "text-right",
          data: "tasa",
        },
        {
          className: "text-right",
          data: "vida_util",
        },
        {
          className: "text-right",
          data: "costo",
        },
        {
          className: "text-right",
          data: "total_depreciado",
        },
        {
          className: "text-right",
          data: "saldo_inicial",
        },
        {
          className: "text-right",
          data: "valor_residual",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDepreciacion(\'' + row.activo_id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + "</div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableHistorialDep").DataTable({
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
      paginate: true,
      responsive: true,
      lengthChange: true,
      autoWidth: true,
      pageLength: 50,
      order: [
        ["1", "desc"],
        ["2", "desc"],
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
          data: "anio",
        },
        {
          data: "mes",
        },
        {
          className: "text-center",
          data: "id_asiento",
          render: function (data, type, row) {
            return row.id_asiento !== null ? '<a href="javascript:;" onclick="visualizarAsiento(\'' + row.id_asiento + "');\">" + row.id_asiento + "</a>" : "";
          },
        },
        {
          data: "observacion",
        },
        {
          className: "text-center",
          data: "estado",
          render: function (data, type, row) {
            var estado =
              row.estado == 1 ? '<label class="text-success">' + $("#activo").val() + "</label>" : '<label class="text-danger">' + $("#inactivo").val() + "</label>";
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
            var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verDetalleDep(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div>';
            if (row.estado == 1) {
              botones += '<a class="dropdown-item" href="javascript:;" onclick="anularActivoFijo(\'' + row.id + "','" + row.estado + "','Depeciacion');\">" + accion + "</a>";
            } else {
              botones += '<a class="dropdown-item" href="javascript:;" style="pointer-events: none; cursor: default;">' + accion + "</a>";
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
  $("#btnGuardarGrupoActivosFijos").click(function () {
    if ($("#nombre").val() == "") {
      mensajes("info", "Ingrese el nombre del tipo");
    } else if ($("#ctamayor").val() == "") {
      mensajes("info", "Seleccione la Cta. Mayor");
    } else if ($("#ctagastos").val() == "") {
      mensajes("info", "Seleccione la Cta. Gasto");
    } else if ($("#ctadepreacum").val() == "") {
      mensajes("info", "Seleccione la Cta. Depreciacion Acumulada");
    } else if ($("#grupo_id").val() == "") {
      mensajes("info", "Seleccione un grupo");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#id_ciudad").change(function () {
    if ($("#id_ciudad").val() != "") {
      $("#ciudad").val($('select[name="id_ciudad"] option:selected').text());
    }
  });
  if ($("#id").val() != "") {
    $("#grupo_id").val(0).trigger("change");
  }
  $("#marca_accesorio").select2({
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
      url: "v3/" + $("#controlador").val() + "/getMarcaComboNuevo",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $("#ctamayor").select2({
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
  $("#ctagastos").select2({
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
  $("#ctadepreacum").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--" + $("#seleccione").val() + "--",
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
  $("#ctamayor").change(function () {
    if ($("#ctamayor").val() != "") {
      var datoCuenta = $("#ctamayor option:selected").text().split(" | ");
      $("#cuentamayor").val(datoCuenta[0]);
    }
  });
  $("#ctagastos").change(function () {
    if ($("#ctagastos").val() != "") {
      var datoCuenta = $("#ctagastos option:selected").text().split(" | ");
      $("#cuentagastos").val(datoCuenta[0]);
    }
  });
  $("#ctadepreacum").change(function () {
    if ($("#ctadepreacum").val() != "") {
      var datoCuenta = $("#ctadepreacum option:selected").text().split(" | ");
      $("#cuantadepreciacion").val(datoCuenta[0]);
    }
  });
  $("#btnGuardarSubtipo").click(function () {
    if ($("#nombre").val() == "") {
      mensajes("info", "Ingrese el nombre del subtipo");
    } else if ($("#codigo").val() == "") {
      mensajes("info", "Ingrese el codigo del subtipo");
    } else if ($("#tipo_id").val() == "") {
      mensajes("info", "Seleccione un tipo");
    } else {
      if ($("#id").val() != "") {
        validarCodigo();
      }
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#btnGuardarDepreciacion").click(function () {
    if ($("#periodo").val() == "") {
      mensajes("info", "Seleccione el periodo");
    } else if (validarDepreciaciones()) {
      mensajes(
        "info",
        "Debe seleccionar por lo menos un activo para continuar..."
      );
    } else if ($("#observacion").val() == "") {
      mensajes("info", "Agregue una observacion");
    } else if ($("#sucursal_empresa").val() == "") {
      mensajes("info", "Agregue una sucursal");
    } else if ($("#punto_emision").val() == "") {
      mensajes("info", "Agregue un punto de emisión");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#btnGuardarActivoFijo").click(function () {
    var regex = /^\d{4}-(0?[1-9]|1[0-2])-(0?[1-9]|1\d|2\d|3[01])$/;
    if ($("#nombre").val() == "") {
      return mensajes("warning", "Ingrese el nombre");
    } else if ($("#tipo").val() == "") {
      return mensajes("warning", "Seleccione el tipo");
    } else if ($("#subtipo").val() == "") {
      return mensajes("warning", "Seleccione el subtipo");
    } else if (!regex.test($("#fecha_compra").val())) {
      return mensajes("warning", "Ingrese la fecha de compra");
    } else {
      mensajes("question", $("#pregunta").val(), "frm_", "action", "");
    }
  });
  $("#tipo").select2({
    width: "100%",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 1,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarTipos",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $("#tipo").change(function () {
    var dt = { id: $("#tipo").val() };
    var subtipo = ajax("v3/" + $("#controlador").val() + "/traerSubtipo", dt, "json");
    if (subtipo.responseJSON !== null) {
      subtipo = subtipo.responseJSON;
      var html = "";
      $.each(subtipo, function (i, item) {
        html += '<option value="' + item.id + '">' + item.nombre + "</option>";
      });
      $("#subtipo").html(html).trigger("change");
    }
  });
  /*$("#responsable").select2({
    width: "100%",
    tags: true,
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 1,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarResponsables",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });*/
  $("#acreedor").select2({
    width: "100%",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getProveedorCombo",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  $("#marca").select2({
    width: "100%",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 1,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getMarcaComboNuevo",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  /*$("#color").select2({
    width: "100%",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getColor",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });*/
  $('#serie_accesorio').select2({
    width: "100%",
    tags: true,
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getSerie",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });
  /*$("#serie").select2({
    width: "100%",
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccionar--",
    minimumInputLength: 1,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getSerie",
      type: "get",
      dataType: "json",
      delay: 250,
      data: function (params) {
        return { term: params.term };
      },
      processResults: function (response) {
        console.log(response);
        return { results: response };
      },
      error(error) {
        console.log(error);
      },
      cache: true,
    },
  });*/
  /*$('#subtipo').select2({
        width: "100%",
        noResults: function () {
            return "No hay resultado";
        },
        searching: function () {
            return "Buscando..";
        },
        placeholder: "--Seleccionar--",
        minimumInputLength: 3,
        ajax: {
            url: "v3/" + $("#controlador").val() + "/buscarSubTipos",
            type: "get",
            dataType: "json",
            delay: 250,
            data: function (params) {
                return { term: params.term };
            },
            processResults: function (response) {
                console.log(response)
                return { results: response };
            },
            error(error) {
                console.log(error);
            },
            cache: true,
        },
    });*/
    /*Dropzone.autoDiscover = false;

    // 2️⃣ Si los elementos ya tienen un Dropzone previo, destrúyelo correctamente
    ["#foto_activo", "#foto_acc"].forEach(id => {
      const el = document.querySelector(id);
      if (el && el.dropzone) {
        el.dropzone.destroy(); // elimina instancia previa correctamente
      }
    });*/
    
      /*// Dropzone de Foto Activo‹‹‹‹‹‹‹‹‹‹‹
      var fileAct = new Dropzone("#foto_activo", {
        url: "#",                
        autoProcessQueue: true, 
        paramName: "fileAct",
        uploadMultiple: false,
        maxFiles: 1,
        addRemoveLinks: true,
        acceptedFiles: "image/*",
        dictRemoveFile: "Remove file",
        init: function () {
          this.on("addedfile", function (file) {
            if (this.files.length > 1) {
              this.removeFile(this.files[0]); // solo 1 imagen
            }
          });
        }
      });
      
      // Dropzone de Foto Accesorio
      var fileAcc = new Dropzone("#foto_acc", {
        url: "#",
        autoProcessQueue: true,
        paramName: "fileAcc",
        uploadMultiple: false,
        maxFiles: 1,
        addRemoveLinks: true,
        acceptedFiles: "image/*",
        dictRemoveFile: "Remove file",
        init: function () {
          this.on("addedfile", function (file) {
            if (this.files.length > 1) {
              this.removeFile(this.files[0]);
            }
          });
        }
      });*/
});
function validarCodigo() {
  if ($("#id").val() != "") {
    return;
  }

  $.ajax({
    url: "v3/" + $("#controlador").val() + "/verificarCodigoSubtipo",
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
function validarMesDepreciacion() { }
function agregarTipoActivosFijos() {
  limpiarModal();
  $(".select2").val("").trigger("change");
  $("#id").val("");
  if ($('#id').val() === '') {
    $('#custom-content-above-accesorios-tab').css('display', 'none');
    $('#custom-content-above-archivos-tab').css('display', 'none');
  } else {
    $('#custom-content-above-accesorios-tab').css('display', 'block');
    $('#custom-content-above-archivos-tab').css('display', 'block');
  }
  $("#tipo_").val('');
  $('#tipo').attr('disabled', false);
  codigo_activo();
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_activos_fijos").html('<i class="fas fa-plus-square"></i> Nuevo Activo Fijo');
  $("#myModal").modal("toggle");
}
function verGruposActivosFijos(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getAF_Tipo",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#id").val(json.id);
      $("#codigo").val(json.codigo);
      $("#nombre").val(json.nombre);
      $("#cuentagastos").val(json.cuentagastos);
      $("#cuantadepreciacion").val(json.cuantadepreciacion);
      $("#grupo_id").val(json.grupo_id).trigger("change");
      $("#tasa").val(json.tasa);
      $("#tipo_tasa").val(json.tipo_tasa).trigger("change");
      $("#vidautil").val(json.vidautil);
      $("#titulo_modal").html(
        '<i class="fas fa-plus-square"></i> Modificar Grupo Activo Fijo'
      );
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/traerCuentaGeneral",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.cuentamayor,
        },
        dataType: "html",
        success: function (html) {
          $("#ctamayor").html(html);
        },
      });
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/traerCuentaGeneral",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.cuentamayor,
        },
        dataType: "html",
        success: function (html) {
          $("#ctagastos").html(html);
        },
      });
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/traerCuentaGeneral",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.cuentamayor,
        },
        dataType: "html",
        success: function (html) {
          $("#ctadepreacum").html(html);
        },
      });
      $("#myModal").modal("toggle");
    },
  });
}
function Subtipos(id) {
  window.location.href = "v3/" + $("#controlador").val() + "/subtipos?id=" + id;
}
function verSubtipo(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getSubtipos",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#id").val(json.id);
      $("#codigo").val(json.codigo);
      $("#nombre").val(json.nombre);
      $("#tipo_id").val(json.tipo_id).trigger("change");
      $("#titulo_modal").html(
        '<i class="fas fa-plus-square"></i> Modificar Subtipo Activo Fijo'
      );
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      $("#myModal").modal("toggle");
    },
  });
  $("#codigo").attr("readonly", "readonly");
}
function agregarSubtipos() {
  limpiarModal();
  $("#codigo").removeAttr("readonly");
  $(".select2").val("").trigger("change");
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fas fa-plus-square"></i> Nuevo Subtipo Activo Fijo'
  );
  $("#myModal").modal("toggle");
}
function verDepreciacion(id) {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getDepreciacionDetalle",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#titulo_modal_depreciacion").html('<i class="fas fa-plus-square"></i> Visualizar Depreciación');
      $("#tablaDetalles").empty();
      tablaHtml = "<tr>";
      for (let i = 0; i < json.length; i++) {
        tablaHtml += "<td>" + json[i].id + "</td><td>" + json[i].anio + "</td><td>" + json[i].mes + "</td><td>" + json[i].fecha + '</td><td><a href="javascript:;" onclick="visualizarAsiento(\'' + json[i].id_asiento + "');\">" + json[i].id_asiento + "</a></td><td>" + json[i].observacion + '</td><td align="right">' + json[i].valordepreciacion + "</tr>";
      }
      $("#tablaDetalles").html(tablaHtml);
      $("#myModal").modal("toggle");
    },
  });
}
function verListadeActivos(id) {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getActivoFijo",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#id").val(json.id);
      if ($('#id').val() === '') {
        $('#custom-content-above-accesorios-tab').css('display', 'none');
        $('#custom-content-above-archivos-tab').css('display', 'none');
      } else {
        $('#custom-content-above-accesorios-tab').css('display', 'block');
        $('#custom-content-above-archivos-tab').css('display', 'block');
      }
      console.log(json);
      $("#nombre").val(json.nombre);
      $("#codigo").val(json.codigo);
      $("#codigo1").val(json.codigo1);
      $("#descripcion").val(json.descripcion);
      //$("#responsable").html('<option value="' + json.responsable + '">' + json.responsable + "</option>").trigger("change");
      $("#responsable").val(json.responsable);
      $("#acreedor").html('<option value="' + json.acreedor + '">' + json.proveedor_nombre + "</option>").trigger("change");
      $("#marca").html('<option value="' + json.marca + '">' + json.marca + "</option>").trigger("change");
      //$("#color").html('<option value="' + json.color + '">' + json.color + "</option>").trigger("change");
      $("#color").val(json.color);
      $("#grupo_id").val(json.grupo_id);
      $("#factura").val(json.factura);
      $("#tipo").html('<option value="' + json.tipo_id + '">' + json.tipo + "</option>").trigger("change");
      $("#tipo_").val(json.tipo_id);
      $('#tipo').attr('disabled', true);
      var dt = { id: json.tipo_id };
      var subtipo = ajax("v3/" + $("#controlador").val() + "/traerSubtipo", dt, "json");
      if (subtipo.responseJSON !== null) {
        subtipo = subtipo.responseJSON;
        var html = "";
        var select = "";
        $.each(subtipo, function (i, item) {
          select = "";
          if (json.subtipo_id == item.id) select = "selected";
          html += '<option value="' + item.id + '" ' + select + ">" + item.nombre + "</option>";
        });
        $("#subtipo").html(html).trigger("change");
      }
      $("#fecha_compra").val(json.fecha_compra);
      $("#modelo").val(json.modelo);
      $("#serie").val(json.serie);
      //$("#serie").html('<option value="' + json.serie + '">' + json.serie + "</option>").trigger("change");
      $("#procedencia").val(json.procedencia);
      $("#estado_activo").val(json.estado_activo);
      $("#estado_activo").trigger("change");
      $("#costo").val(json.costo);
      $("#estado").val(json.estado);
      $("#ubicacion").val(json.ubicacion);
      $("#depreciacion_acum").val(json.depreciacion_acum);
      $("#tasa").val(json.tasa);
      $("#tipo_tasa").val(json.tipo_tasa);
      $("#tipo_tasa").trigger("change");
      $("#dias_depreciacion").val(json.dias_depreciacion);
      $("#dias_depreciacion").trigger("change");
      $("#valor_residual").val(json.valor_residual);
      $("#nota").val(json.nota);
      $("#vida_util").val(json.vida_util);
      var data = { id: json.id };
      var html = ajax("v3/utilidades/dropzone", data, "html");
      if (html.responseText != "") {
        html = html.responseText;
      }
      $("#titulo_activos_fijos").html('<i class="fas fa-edit"></i> Editar activo fijo: ' + json.nombre);
      $("#custom-content-above-archivos").html(html);
      $('#tableAccesorios').dataTable().fnClearTable();
      $('#tableAccesorios').dataTable().fnDestroy();
      $("#tableAccesorios").DataTable({
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
        responsive: true,
        lengthChange: false,
        autoWidth: true,
        pageLength: 100,
        order: [[0, "desc"]],
        ajax: {
          url: "v3/" + $("#controlador").val() + "/traerAccesorios",
          type: "post",
          data: {
            id: json.id
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
            data: "nombre",
          },
          {
            data: "marca",
          },
          {
            data: "modelo",
          },
          {
            data: "serie",
          },
          {
            className: "text-center",
            data: "id",
            render: function (data, type, row) {
              var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
              if (row.estado == 1) {
                accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
              }
              var botones = '<div class="btn-group hero-accion"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verAccesorio(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="eliminarAccesorio(\'' + row.id + '\')"><i class="fa fa-trash" style="color:red;"></i> Eliminar</a></div></div>';
              return botones;
            },
          },
        ],
      });
      $("#myModal").modal("toggle");
    },
  });
}
function verAccesorio(id) {
  var data = {
    id: id
  };
  var json = ajax("v3/" + $("#controlador").val() + "/traerAccesorio", data, "json");
  if (json.responseJSON !== '') {
    json = json.responseJSON;
    $('#id_accesorio').val(json.nombre);
    $('#nombre_accesorio').val(json.nombre);
    $('#marca_accesorio').val(json.marca);
    $('#modelo_accesorio').val(json.modelo);
    $('#serie_accesorio').val(json.serie);
  }
}
function HistorialDepreciacioes() {
  window.open(
    "v3/" + $("#controlador").val() + "/historialDepreciacion",
    "_self"
  );
}
function verDetalleDep(id) {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/geDetHistorialDep",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#titulo_modal").html(
        '<i class="fas fa-plus-square"></i> Visualizar Depreciación'
      );
      $("#tablaDetalles").empty();
      tablaHtml = "<tr>";
      for (let i = 0; i < json.length; i++) {
        tablaHtml +=
          "<td>" +
          json[i].activo_id +
          "</td><td>" +
          json[i].nombre +
          '</td><td align="right">' +
          json[i].valordepreciacion +
          "</td></tr>";
      }
      $("#tablaDetalles").html(tablaHtml);
      $("#myModal").modal("toggle");
    },
  });
}
function CrearDep() {
  window.open("v3/" + $("#controlador").val() + "/crearDepreciacion", "_self");
}
function buscarDatosDep() {
  $("#tableListaDepreciaciones").dataTable().fnClearTable();
  $("#tableListaDepreciaciones").dataTable().fnDestroy();
  $("#tableListaDepreciaciones").DataTable({
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
    paginate: false,
    responsive: false,
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
      data: {
        tipo: $("#tipo").val(),
        periodo: $("#periodo").val(),
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
        data: "id_activo",
        render: function (data, type, row) {
          var checked = "";
          if (row.dep == "SI") {
            checked = "checked disabled";
          }
          //return '<input type="hidden" name="checkids[]" value="' + row.id_activo + '"/> <input type="checkbox" class="checDep" id="id_check" name="id_check[]" value="' + row.id_activo + '"></input>';
          return ('<div class="icheck-primary d-inline"><input type="checkbox" class="checDep" id="id_check|' +row.id_activo +"|" +row.vida_util +
            "|" +
            row.tasa +
            "|" +
            row.costo +
            "|" +
            row.acumulado +
            "|" +
            row.valor_dep_mes +
            "|" +
            row.saldo_despues +
            '" name="id_check|' +
            row.id_activo +
            "|" +
            row.vida_util +
            "|" +
            row.tasa +
            "|" +
            row.costo +
            "|" +
            row.acumulado +
            "|" +
            row.valor_dep_mes +
            "|" +
            row.saldo_despues +
            '" value="' +
            row.id_activo +
            '" ' +
            checked +
            '><label for="id_check|' +
            row.id_activo +
            "|" +
            row.vida_util +
            "|" +
            row.tasa +
            "|" +
            row.costo +
            "|" +
            row.acumulado +
            "|" +
            row.valor_dep_mes +
            "|" +
            row.saldo_despues +
            '" /></label></div>'
          );
        },
      },
      {
        data: "fecha_compra",
        render: function (data, type, row) {
          return getfecha(row.fecha_compra, null, "-");
        },
      },
      {
        data: "codigo",
      },
      {
        data: "nombre",
      },
      {
        data: "tipo",
      },
      {
        className: "text-center",
        data: "documento",
      },
      {
        className: "text-center",
        data: "dias_transcurridos",
      },
      {
        className: "text-right",
        data: "vida_util",
        render: function (data, type, row) {
          if (row.dep == "SI") {
            return row.vida_util;
          } else {
            //return row.vida_util + '<input type="hidden" id="vida_util" name="vida_util[]" value="' + row.vida_util + '"></input>';
            return row.vida_util;
          }
        },
      },
      {
        className: "text-right",
        data: "tasa",
        render: function (data, type, row) {
          if (row.dep == "SI") {
            return row.tasa;
          } else {
            //return row.tasa + '<input type="hidden" id="tasa" name="tasa[]" value="' + row.tasa + '"></input>';
            return row.tasa;
          }
        },
      },
      {
        className: "text-right",
        data: "costo",
        render: function (data, type, row) {
          if (row.dep == "SI") {
            return row.costo;
          } else {
            //return row.costo + '<input type="hidden" id="costo" name="costo[]" value="' + row.costo + '"></input>';
            return row.costo;
          }
        },
      },
      {
        className: "text-right",
        data: "acumulado",
        render: function (data, type, row) {
          if (row.dep == "SI") {
            return row.acumulado;
          } else {
            //return row.acumulado + '<input type="hidden" id="acumulado" name="acumulado[]" value="' + row.acumulado + '"></input>';
            return row.acumulado;
          }
        },
      },
      {
        className: "text-right",
        data: "saldo_actual",
      },
      {
        className: "text-right",
        data: "valor_dep_mes",
        render: function (data, type, row) {
          if (row.dep == "SI") {
            return parseFloat(row.valor_dep_mes).toFixed(2);
          } else {
            //return parseFloat(row.valor_dep_mes).toFixed(2) + '<input type="hidden" id="valor_dep_mes" name="valor_dep_mes[]" value="' + parseFloat(row.valor_dep_mes).toFixed(2) + '"></input>';
            return parseFloat(row.valor_dep_mes).toFixed(2);
          }
        },
      },
      {
        className: "text-right",
        data: "saldo_despues",
        render: function (data, type, row) {
          if (row.dep == "SI") {
            return parseFloat(row.saldo_despues).toFixed(2);
          } else {
            //return parseFloat(row.saldo_despues).toFixed(2) + '<input type="hidden" id="saldo_despues" name="saldo_despues[]" value="' + parseFloat(row.saldo_despues).toFixed(2) + '"></input>';
            return parseFloat(row.saldo_despues).toFixed(2);
          }
        },
      },
    ],
  });
}
function validarDepreciaciones() {
  var estado = false;
  var checks = document.getElementsByClassName("checDep");
  var count = 0;
  $.each(checks, function (i, item) {
    if ($(item).is(":checked")) {
      count++;
    }
  });
  if (count == 0) {
    estado = true;
  }
  return estado;
}
function anularActivoFijo(id) {
  Swal.fire({
    title: "¿Desea anular este activo?",
    text: $("#informa").val(),
    icon: "info",
    showCancelButton: true,
    cancelButtonText: "No",
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Si",
  }).then((result) => {
    if (result.isConfirmed) {
      testCruceValores(id);
    }
  });
}
async function testCruceValores(id) {
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
        url: "v3/" + $("#controlador").val() + "/estadoDepeciacion",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: id,
          observacion: text,
        },
        datatype: "json",
        success: function (json) {
          console.log(json);
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
function codigo_activo() {
  if ($('#id').val() === '') {
    var tipo = $("#tipo").val();
    var data = {
      tipo: tipo,
    };
    var json = ajax("v3/" + $("#controlador").val() + "/CodigoAf", data, "json");
    if (json.responseJSON != null) {
      json = json.responseJSON;
      $("#codigo").val(json.codigo);
      $("#codigo1").val(json.secuencia);
      $("#tasa").val(json.tasa);
      $("#vida_util").val(json.vida_util);
    }
  }
}
function valor_costo_compra() {
  var e = parseFloat($("#costo").val());
  if (e == "" || isNaN(e) || e <= 0) {
    e = 0;
  }
  $("#costo").val(parseFloat(e).toFixed(2, 2));
}
function valor_dep_acumulada() {
  var e = parseFloat($("#depreciacion_acum").val());
  if (e == "" || isNaN(e) || e <= 0) {
    e = 0;
  }
  $("#depreciacion_acum").val(parseFloat(e).toFixed(2, 2));
}
function valor_resi() {
  var e = parseFloat($("#valor_residual").val());
  if (e == "" || isNaN(e) || e <= 0) {
    e = 0;
  }
  $("#valor_residual").val(parseFloat(e).toFixed(2, 2));
}
function agregarAccesorio() {
  $('#nombre_accesorio').attr('readonly', false);
  $('#nombre_accesorio').val('');
  $('#modelo_accesorio').attr('readonly', false);
  $('#modelo_accesorio').val('');
  $('#marca_accesorio').attr('disabled', false);
  $('#marca_accesorio').val('').trigger('change');
  $('#serie_accesorio').attr('disabled', false);
  $('#serie_accesorio').val('').trigger('change');
  $('#agregarAccesorio').toggleClass('btn-primary btn-danger');
  if ($('#agregarAccesorio').hasClass('btn-danger')) {
    $('#agregarAccesorio').html('<i class="fas fa-times-circle"></i> <span class="botones">Cancelar</span>');
    $('#agregarAccesorio').attr('onclick', 'cancelarAccesorio();');
    $('#guardarAccesorio').show();
  } else {
    $('#agregarAccesorio').html('<i class="fas fa-plus-circle"></i> <span class="botones">Agregar</span>');
    $('#guardarAccesorio').hide();
  }
}
function cancelarAccesorio() {
  $('#nombre_accesorio').attr('readonly', true);
  $('#nombre_accesorio').val('');
  $('#modelo_accesorio').attr('readonly', true);
  $('#modelo_accesorio').val('');
  $('#marca_accesorio').attr('disabled', true);
  $('#marca_accesorio').val('').trigger('change');
  $('#serie_accesorio').attr('disabled', true);
  $('#serie_accesorio').val('').trigger('change');
  $('#agregarAccesorio').toggleClass('btn-danger btn-primary');
  $('#agregarAccesorio').html('<i class="fas fa-plus-circle"></i> <span class="botones">Agregar</span>');
  $('#agregarAccesorio').attr('onclick', 'agregarAccesorio();');
  $('#guardarAccesorio').hide();
}
function guardarAccesorio() {
  if ($('#nombre_accesorio').val() == '') {
    mensajes('warning', 'Ingrese el nombre del accesorio');
  }
  else {
    //mensajes("question", $("#pregunta").val(), "frm_", "actionAccesorios", "");
    $.ajax({
      url: 'v3/activosfijos/saveAccesorios',
      type: 'POST',
      data: $('#frm_').serialize(),
      dataType: 'json',
      success: function (json) {
        $('.tablaDatos').DataTable().ajax.reload();
        cancelarAccesorio();
        mensajes('success', json.message, "frm_", action);
      },
      error: function (err) {
        console.error('Error:', err);
      }
    });
    
  }
}
function eliminarAccesorio(id) {
  var data = { id: id };
  var json = ajax("v3/" + $("#controlador").val() + "/eliminarAccesorio", data, "json");
  if (json.responseJSON != null) {
    json = json.responseJSON;
    mensajes(json.status, json.message);
    $('#tableAccesorios').DataTable().ajax.reload();
  }
}
function descargarPDFActivo(id) {
  var url = "v3/" + $("#controlador").val() + "/descargarPDFActivo?id=" + encodeURIComponent(id);
  window.open(url, "_blank");
}
function descargarBarcodePDFActivo(id) {
  var url = "v3/" + $("#controlador").val() + "/descargarBarcodePDFActivo?id=" + encodeURIComponent(id);
  window.open(url, "_blank");
}
function excelDepreciacionAcumulada(id) {
  var url = "v3/" + $("#controlador").val() + "/excelDepreciacionAcumulada?id=" + encodeURIComponent(id);
  window.open(url, "_blank");
}