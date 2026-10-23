var nombre_menu_submenu = "";
var btnCed = $(".ladda-button-demo-ced").ladda();
var btnCer = $(".ladda-button-demo-cer").ladda();
var btnUsuarioLogo = $(".ladda-button-demo-usuariologo").ladda();
$(document).ready(function () {

  $(".modal").on("hidden.bs.modal", function () {
    $("#nombre_rol").val("");
  });
  var btnFirma = $(".ladda-button-demo-firma").ladda();
  btnFirma.click(function () {
    btnFirma.ladda("start");
  });
  btnCed.click(function () {
    btnCed.ladda("start");
  });
  btnCer.click(function () {
    btnCer.ladda("start");
  });
  btnUsuarioLogo.click(function () {
    btnCer.ladda("start");
  });
  var tableInfoTributaria = "";
  try {
    $("#tableRolesUsuarios").DataTable();
    $("#tableBodegas").DataTable({
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
      iDisplayLength: 25,
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
          //window.open('v3/admin', '_self');
          console.log(error);
        },
      },
      columns: [
        {
          data: "id",
        },
        {
          data: "nombre",
        },
        {
          data: "departamento",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
            return estado;
          },
        },
        {
          data: "id",
        },
      ],
    });
    $("#tableMenu").DataTable({
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
        [25, 50, 100, -1],
        [25, 50, 100, "All"],
      ],
      iDisplayLength: 100,
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo").val() + "js",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        error() {
          window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id_menu",
        },
        {
          data: "nombre_menu",
        },
        {
          className: "text-center",
          data: "icon_menu",
        },
        {
          data: "controlador_menu",
        },
        {
          className: "text-center",
          data: "orden_menu",
        },
        {
          className: "text-center",
          data: "estado_menu",
          render: function (data, type, row) {
            var estado =
              row.estado_menu == 1
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
          data: "id_menu",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado_menu == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verMenu(\'' + row.id_menu + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><a class="dropdown-item" href="javascript:;" onclick="subMenu(\'' + row.id_menu + '\');"><i class="fas fa-plus-circle" style="color:blue;"></i> Submenú</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id_menu + "','" + row.estado_menu + "','Menu');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableSubMenu").DataTable({
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
        [25, 50, 100, -1],
        [25, 50, 100, "All"],
      ],
      iDisplayLength: 100,
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo").val() + "js",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: $("#id_menu_").val(),
        },
        error() {
          window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id_menu",
        },
        {
          data: "nombre_menu",
        },
        {
          data: "nombre_submenu",
        },
        {
          className: "text-center",
          data: "icon_menu",
        },
        {
          data: "controlador_menu",
        },
        {
          data: "funcion_menu",
        },
        {
          className: "text-center",
          data: "orden_menu",
        },
        {
          className: "text-center",
          data: "estado_menu",
          render: function (data, type, row) {
            var estado = row.estado_menu == 1 ? '<label class="text-success">' + $("#activo").val() + "</label>" : '<label class="text-danger">' + $("#inactivo").val() + "</label>";
            return estado;
          },
        },
        {
          className: "text-center",
          data: "estado_menu",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> Activar';
            if (row.estado_menu == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> Desactivar';
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verSubmenu(\'' + row.id_menu + '\');"><i class="fas fa-eye" style="color:green;"></i> Ver</a><a class="dropdown-item" href="javascript:;" onclick="subMenu(\'' + row.id_menu + '\');"><i class="fas fa-plus-circle" style="color:blue;"></i> Submenú</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id_menu + "','" + row.estado_menu + "','Menu');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableEmpresas").DataTable({
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
        error() {
          window.open("v3/admin", "_self");
        },
      },
      columns: [
        {
          data: "id",
        },
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
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> Activar';
            if (row.estado == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> Desactivar';
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verEmpresa(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> Ver</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Empresa');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableUsuarios").DataTable({
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
      pageLength: 100,
      processing: true,
      serverSide: true,
      dom: '<"float-left"B><"float-left"l><"float-right"f>t<"float-left"i><"float-right"p><"clearfix">',
      buttons: [
        { extend: "copy", text: '<i class="fa fa-copy" title="Copy"></i>', },
        { extend: "excel", title: tituloExcel, text: '<i class="fas fa-file-excel" title="Excel"></i>', },
        { extend: "pdf", title: tituloExcel, text: '<i class="fas fa-file-pdf" title="PDF"></i>', },
        { extend: "print", text: '<i class="fa fa-print" title="Print"></i>', customize: function (win) { $(win.document.body).addClass("white-bg"); $(win.document.body).css("font-size", "10px"); $(win.document.body).find("table").addClass("compact").css("font-size", "inherit"); }, },
      ],
      ajax: {
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        error(error) {
          console.log(error);
          //window.open('v3/admin', '_self');
        },
      },
      columns: [
        {
          className: "text-center",
          data: "imagen_url",
          render: function (type, data, row) {
            var logo = '<i class="fas fa-user-tie fa-3x" style="color:#91B2F3;"></i>';
            if (row.imagen_url !== null) {
              logo = '<img src="avatars/' + row.imagen_url + '" style="width: 70px;cursor:pointer;" onclick="mostrarImg(\'avatars/' + row.imagen_url + "')\"/>";
            }
            return logo;
          },
        },
        {
          data: "id",
        },
        {
          data: "id",
          render: function (data, type, row) {
            return (row.nombre1 + " " + row.nombre2 + " " + row.apellido1 + " " + row.apellido2);
          },
        },
        {
          data: "direccion",
        },
        {
          data: "email",
        },
        {
          data: "tipo_usuario",
        },
        {
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
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
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verUsuario(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><a class="dropdown-item" href="javascript:;" onclick="verPerfilUsuario(\'' + row.id + '\');"><i class="fas fa-id-card" style="color:gray;"></i> Perfil</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Usuario');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableEstablecimientos").DataTable({
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
    });
    $("#tableCatalogo").DataTable({
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + "js?id=" + $("#id_subcatalogo").val(),
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
          data: "id_catalogo",
        },
        {
          data: "catalogo",
        },
        {
          data: "valor",
        },
        {
          data: "id_catalogo",
          className: "text-center",
          render: function (data, type, row) {
            var cadena = row.ucnombre1;
            var letra = cadena.charAt(0);
            return letra + ". " + row.ucapellido1;
          },
        },
        {
          data: "id_catalogo",
          className: "text-center",
          render: function (data, type, row) {
            var cadena = row.umnombre1;
            var letra = cadena.charAt(0);
            return letra + ". " + row.umapellido1;
          },
        },
        {
          className: "text-center",
          data: "created_at",
        },
        {
          className: "text-center",
          data: "updated_at",
        },
        {
          className: "text-center",
          data: "id_catalogo",
          render: function (data, type, row) {
            var estado =
              row.estado_catalogo == 1
                ? '<label class="text-success">Activo</label>'
                : '<label class="text-danger">Inactivo</label>';
            return estado;
          },
        },
        {
          className: "text-center",
          data: "id_catalogo",
          render: function (data, type, row) {
            var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
            if (row.estado_catalogo == 1) {
              accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
            }
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="veCatalogo(\'' + row.id_catalogo + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><a class="dropdown-item" href="javascript:;" onclick="verSubCatalogo(\'' + row.id_catalogo + '\');"><i class="fas fa-plus-circle" style="color:blue;"></i> Subcatálogo</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id_catalogo + "','" + row.estado_catalogo + "','Catalogo');\">" + accion + "</a></div></div>";
            if ($("#id_subcatalogo").val() != "") {
              botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="veCatalogo(\'' + row.id_catalogo + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id_catalogo + "','" + row.estado_catalogo + "','Catalogo');\">" + accion + "</a></div></div>";
            }
            return botones;
          },
        },
      ],
    });
    $("#tablePlanes").DataTable({
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + 'js',
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
          data: "nombre",
        },
        {
          data: "tiempo",
        },
        {
          data: "valor",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
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
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verPlan(\'' + row.id + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + '</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Plan');\">" + accion + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    $("#tableSeleccionarPlanes").DataTable({
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
        url: "v3/" + $("#controlador").val() + "/" + $("#submodulo_").val() + 'js',
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
          data: "razonsocial",
        },
        {
          data: "nombre",
        },
        {
          data: "tiempo",
        },
        {
          data: "valor",
        },
        {
          data: "valor_pagado",
        },
        {
          className: "text-center",
          data: "id",
          render: function (data, type, row) {
            var estado = row.estado == 1 ? '<label class="text-success">Activo</label>' : '<label class="text-danger">Inactivo</label>';
            return estado;
          },
        },
      ],
    });
    $("#tableAuditoria").DataTable({
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
      pageLength: 100,
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
          data: "id_auditoria",
        },
        {
          data: "sistema_auditoria",
        },
        {
          data: "navegador_auditoria",
        },
        {
          data: "controlador_auditoria",
        },
        {
          data: "submodulo_auditoria",
        },
        {
          data: "modelo_auditoria",
        },
        {
          data: "vista_auditoria",
        },
        {
          className: "text-center",
          data: "id_auditoria",
          render: function (data, type, row) {
            var cadena = row.nombre1;
            var letra = cadena.charAt(0);
            return letra + ". " + row.apellido1;
          },
        },
        {
          className: "text-center",
          data: "created_at_auditoria",
        },
        {
          className: "text-center",
          data: "id_auditoria",
          render: function (data, type, row) {
            var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verAuditoria(\'' + row.id_auditoria + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $("#ver").val() + "</a></div></div>";
            return botones;
          },
        },
      ],
    });
    tableInfoTributaria = $("#tableInfoTributaria").DataTable({
      language: {
        decimal: "",
        emptyTable: $("#emptyTable").val(),
        info: $("#mostrando").val() + " _START_ " + $("#a").val() + " _END_ " + $("#de").val() + " _TOTAL_ " + $("#entradas").val(),
        infoEmpty: $("#mostrando").val() +
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
    });
  } catch (error) {

  }
  $("#usuarioLogo").change(function (e) {
    if (($("#usuarioLogo").val().length = 0)) {
      btnUsuarioLogo.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("usuarioLogo");
      var file = inputFileImage.files[0];
      var fileSize = $("#usuarioLogo")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnUsuarioLogo.ladda("stop");
        $("#usuarioLogo").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("usuarioLogo", file);
        fd.append("tipo_archivo", "usuarioLogo");
        fd.append("id_usuario", $("#id").val());
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
              btnUsuarioLogo.ladda("stop");
              $("#usuarioLogo").val("");
              $("#logousuario").html(imagen[1]);
              $('.tablaDatos').DataTable().ajax.reload();
            } else {
              btnUsuarioLogo.ladda("stop");
              alert(imagen[1]);
              $("#usuarioLogo").val("");
            }
          },
        });
      }
    }
  });
  $("#btnGuardarCatalogo").click(function () {
    if ($("#catalogo").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre del catálogo...");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#btnGuardarSeleccionPlan").click(function () {
    if ($("#id_plan").val() == "") {
      return mensajes("warning", "Debe ingresar el plan que desea adquirir...");
    } else if ($("#id_empresa").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre de la Empresa...");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#btnGuardarEstablecimiento").click(function () {
    if ($("#nombre").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre del establecimiento...");
    } else if ($("#establecimiento").val() == "") {
      return mensajes("warning", "Debe ingresar el número del establecimiento...");
    } else if ($("#emision").val() == "") {
      return mensajes("warning", "Debe ingresar el número del punto de emisión...");
    } else if ($("#direccion").val() == "") {
      return mensajes("warning", "Debe ingresar la direccion del punto de emisión...");
    } else {
      mensajes("question", $("#pregunta").val());
    }
  });
  $("#btnGuardarMenu").click(function () {
    if ($("#nombre_menu").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre del menú...");
    } else if ($("#controlador_menu").val() == "") {
      return mensajes("warning", "Debe ingresar el controlador del menú...");
    } else if ($("#orden_menu").val() == "") {
      return mensajes("warning", "Debe ingresar el orden del menú...");
    } else if ($("#icon_menu").val() == "") {
      return mensajes("warning", "Debe ingresar el icono del menú...");
    } else {
      mensajes("question", $("#pregunta").val(), 'frm_', 'action', '', 'tabla');
    }
  });
  $("#btnGuardarSubMenu").click(function () {
    if ($("#nombre_submenu").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre del submenú...");
    } else if ($("#controlador_submenu").val() == "") {
      return mensajes("warning", "Debe ingresar el controlador del submenú...");
    } else if ($("#orden_submenu").val() == "") {
      return mensajes("warning", "Debe ingresar el orden del submenú...");
    } else if ($("#icon_submenu").val() == "") {
      return mensajes("warning", "Debe ingresar el icono del submenú...");
    } else {
      mensajes("question", $("#pregunta").val(), 'frm_', 'action', '', 'tabla');
    }
  });
  $("#btnGuardarPermisos").click(function () {
    if ($("#id_usuario_permiso").val() == "") {
      return mensajes("warning", "Debe seleccionar un usuario...");
    } else if ($("#id_submenu_permiso").val() == "") {
      return mensajes("warning", "Debe seleccionar algun permiso...");
    }
    mensajes("question", $("#pregunta").val(), 'frm_', 'action', '');
  });
  $("#btnGuardarEmpresa").click(function () {
    var numCond = $("#id").val().substr(0, 1);
    var idEmpresa = $("#id").val();
    if (numCond == "0") {
      idEmpresa = $("#id").val().substr(1, $("#id").val().length);
    }
    if (!Number.isInteger(parseFloat(idEmpresa.trim()))) {
      return mensajes("warning", "El ruc debe ser solo numeros");
    } else if ($("#id").val().length != 13) {
      return mensajes("warning", "El ruc de la empresa debe contener 13 dígitos");
    } else if ($("#razonsocial").val() == "") {
      return mensajes("warning", "Debe ingresar la razon social de la empresa");
    } else if ($("#nombrecomercial").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre comercial de la empresa");
    } else if ($("#email").val() == "") {
      return mensajes("warning", "Debe ingresar una dirección de correo de la empresa");
    } else if ($("#nombre_corto").val() == "") {
      return mensajes("warning", "Debe ingresar un nombre corto para la empresa");
    } else if ($("#pais").val() == "") {
      return mensajes("warning", "Seleccione un país para la empresa");
    } else if ($("#provincia").val() == "") {
      return mensajes("warning", "Seleccione una provincia para la empresa");
    } else if ($("#id_ciudad").val() == "") {
      return mensajes("warning", "Seleccione una ciudad para la empresa");
    } else if ($("#direccion").val() == "") {
      return mensajes("warning", "Debe ingresar una dirección de la empresa");
    } else if ($("#tipo_contribuyente").val() == "") {
      return mensajes("warning", "Seleccione el tipo de contribuyente de la empresa");
    } else if ($("#agente_retencion").val() == "") {
      return mensajes("warning", "Seleccione si es agente de retención");
    } else if ($("#ambiente").val() == "") {
      return mensajes("warning", "Seleccione el ambiente de la facturación electrónica");
    } else if ($("#contabilidad").val() == "") {
      return mensajes("warning", "Seleccione si la empresa esta obligada a llevar contabilidad");
    } else if ($("#tipo_rimpe").val() == "") {
      return mensajes("warning", "Seleccione el regimen de la empresa");
    } else if ($("#entidadFirma").val() != "" && $("#clave_firma").val() == "") {
      return mensajes("warning", "Ingrese la clave de la firma electrónica");
    } else {
      var resul = validarDocumento($("#id").val());
      if (resul === false) {
        return;
      }
    }
    mensajes("question", $("#pregunta").val());
  });
  $("#id_usuario_permiso").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/buscarUsuario",
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
        //window.open('v3/admin', '_self');
        console.log(error);
      },
      cache: true,
    },
  });
  $("#id_usuario_permiso").change(function () {
    if ($("#id_usuario_permiso").val() != "") {
      $("#id_submenu_permiso").prop("disabled", false);
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/getPermisos",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: $("#id_usuario_permiso").val(),
        },
        dataType: "json",
        success: function (json) {
          if (json != "") {
            var ids = JSON.parse(json.id_submenu_permiso);
            $("#id_permiso").val(json.id_permiso);
            $("#id_usuario_permiso").val(json.id_usuario_permiso);
            $("#id_usuario_creacion_permiso").val(
              json.id_usuario_creacion_permiso
            );
            $("#id_usuario_modificacion_permiso").val(
              json.id_usuario_modificacion_permiso
            );
            $("#id_submenu_permiso").val(ids).trigger("change");
          } else {
            var ids = "";
            $("#id_usuario_permiso").val($("#id_usuario_permiso").val());
            $("#id_usuario_creacion_permiso").val(
              json.id_usuario_creacion_permiso
            );
            $("#id_usuario_modificacion_permiso").val(
              json.id_usuario_modificacion_permiso
            );
            $("#id_submenu_permiso").val(ids).trigger("change");
          }
        },
        error(error) {
          console.log(error);
          //window.open('v3/admin', '_self');
        },
      });
    } else {
      $("#id_submenu").prop("disabled", true);
      $("#id_submenu").val("").trigger("change");
    }
  });
  $("#id_pertenece_menu").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getAllMenus",
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
  $("#id_pertenece_menu").change(function () {
    if ($("#id_pertenece_menu").val() == "") {
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/menuCombo",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: $("#id_menu_").val(),
        },
        dataType: "html",
        success: function (html) {
          $("#id_pertenece_menu").append(html).trigger("change");
          nombre_menu_submenu = $('select[name="id_pertenece_menu"] option:selected').text();
          $("#titulo_modal").html('<i class="fas fa-plus-square"></i> Nuevo ' + $("#submodulo").val() + "/" + nombre_menu_submenu);
        },
        error(error) {
          console.log(error);
        },
      });
    }
  });
  $("#id_representante").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getRepresentante",
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
  $("#id_contador").select2({
    noResults: function () {
      return "No hay resultado";
    },
    searching: function () {
      return "Buscando..";
    },
    placeholder: "--Seleccione--",
    minimumInputLength: 3,
    ajax: {
      url: "v3/" + $("#controlador").val() + "/getRepresentante",
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
  $("#entidadFirma").change(function (e) {
    if (($("#entidadFirma").val().length = 0)) {
      btnFirma.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("entidadFirma");
      var file = inputFileImage.files[0];
      var fileSize = $("#entidadFirma")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnFirma.ladda("stop");
        $("#entidadFirma").val("");
        alert("Error: imagen muy grande o tiene una resolucion muy alta");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("entidadFirma", file);
        fd.append("tipo_archivo", "entidadFirma");
        fd.append("id_empresa", $("#id").val());
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
              console.log(imagen[1]);
              btnFirma.ladda("stop");
              $("#entidadFirma").val("");
              $("#lblEntidadFirma").html(
                '<input type="file" name="entidadFirma" id="entidadFirma" style="display:none"><i class="fa-regular fa-thumbs-up"></i> Añadir archivo'
              );
              $("#ruta_firma").val(imagen[1]);
            } else {
              btnFirma.ladda("stop");
              alert(imagen[1]);
              $("#entidadFirma").val("");
            }
          },
        });
        btnFirma.ladda("stop");
      }
    }
  });
  $('#chenewsystem').change(function () {
    if ($('#chenewsystem').is(':checked')) {
      $('#new_system').val(1);
    } else {
      $('#new_system').val(0);
    }
  });
  $("#entidadRuc").change(function (e) {
    if (($("#entidadRuc").val().length = 0)) {
      btnRuc.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("entidadRuc");
      var file = inputFileImage.files[0];
      var fileSize = $("#entidadRuc")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnRuc.ladda("stop");
        $("#entidadRuc").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("entidadRuc", file);
        fd.append("tipo_archivo", "entidadRuc");
        fd.append("id_empresa", $("#id").val());
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
              $("#entidadRuc").val("");
              $("#lblRuc").html(
                'Ruc: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=entidadRuc" target="new">ver/descargar</a>'
              );
            } else {
              btnRuc.ladda("stop");
              alert(imagen[1]);
              $("#entidadRuc").val("");
            }
          },
        });
      }
    }
  });
  $("#btnGuardarPerfilUsuario").click(function () {
    mensajes("question", $("#pregunta").val());
  });
  $("#btnGuardarPerfil").click(function () {
    mensajes("question", $("#pregunta").val());
  });
  $("#establecimiento").blur(function () {
    $.ajax({
      url: "v3/utilidades/validar_punto",
      type: "post",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      data: {
        e: $("#establecimiento").val(),
      },
      dataType: "html",
      success: function (html) {
        $("#emision").val(html);
      },
    });
  });
  $("#usuarioFoto").change(function (e) {
    if (($("#usuarioFoto").val().length = 0)) {
    } else {
      var inputFileImage = document.getElementById("usuarioFoto");
      var file = inputFileImage.files[0];
      var fileSize = $("#usuarioFoto")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        $("#usuarioFoto").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("usuarioFoto", file);
        fd.append("tipo_archivo", "usuarioFoto");
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
              $("#usuarioFoto").val("");
              $("#fotoUsuario").html(imagen[1]);
            } else {
              $("#usuarioFoto").val("");
            }
          },
        });
      }
    }
  });
  $("#btnGuardarRol").click(function () {
    if ($("#nombre_rol").val() == "") {
      return mensajes("warning", "Debe ingresar el nombre del rol");
    } else {
      mensajes("question", $("#pregunta").val());
    }


  });
  $("#btnGuardarUsuario").click(function () {
    var clave = $("#password").val();
    var confirmarClave = $("#conf_password").val();
    if ($("#nombre1").val() == "") {
      mensajes("info", "Ingrese el nombre");
    } else if ($("#apellido1").val() == "") {
      mensajes("info", "Ingrese el primer apellido");
    } else if ($("#apellido2").val() == "") {
      mensajes("info", "Ingrese el segundo apellido");
    } else if ($("#id").val() == "") {
      mensajes("info", "Ingrese la cedula");
    } else if ($("#ocupacion").val() == "") {
      mensajes("info", "Ingrese la Ocupación");
    } else if ($("#fecha_nacimiento").val() == "") {
      mensajes("info", "Ingrese la Fecha de Nacimiento");
    } else if ($("#email").val() == "") {
      mensajes("info", "Ingrese el correo");
    } else if ($("#password").val() == "" && $("#edit_user").val() == "") {
      mensajes("info", "Ingrese la contraseña");
    } else if (clave !== confirmarClave) {
      mensajes("info", "La confirmación de la clave es incorrecta");
    } else if ($("#id_tipo_usuario").val() == "") {
      mensajes("info", "Seleccione el tipo de usuario");
    } else if ($("#id_tipo_usuario").val() == "3" && $("#especialidad").val() == "") {
      mensajes("info", "Seleccione la especialidad");
    } else if ($("#pais").val() == "") {
      mensajes("info", "Seleccione el pais");
    } else if ($("#direccion").val() == "") {
      mensajes("info", "Ingrese la direccion");
    } else {
      if ($("#edit_user").val() == "") {
        validarCorreo();
      } else {
        mensajes("question", $("#pregunta").val());
      }
    }
  });
  $("#id_tipo_usuario").change(function () {
    if ($(this).val() === "3") {
      $("#div_especialidad").show();
      $("#div_color").show();
    } else {
      $("#div_especialidad").hide();
      $("#div_color").hide();
    }
  });
  $("#usuarioCedula").change(function (e) {
    if (($("#usuarioCedula").val().length = 0)) {
      btnCed.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("usuarioCedula");
      var file = inputFileImage.files[0];
      var fileSize = $("#usuarioCedula")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnCed.ladda("stop");
        $("#usuarioCedula").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("usuarioCedula", file);
        fd.append("tipo_archivo", "usuarioCedula");
        fd.append("id_usuario", $("#id").val());
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
              $("#usuarioCedula").val("");
              $("#lblCedula").html('Cedula: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=usuarioCedula" target="new">ver/descargar</a>');
            } else {
              btnCed.ladda("stop");
              $("#usuarioCedula").val("");
            }
          },
        });
      }
    }
  });
  $("#usuarioVotacion").change(function (e) {
    if (($("#usuarioVotacion").val().length = 0)) {
      btnCer.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("usuarioVotacion");
      var file = inputFileImage.files[0];
      var fileSize = $("#usuarioVotacion")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnCer.ladda("stop");
        $("#usuarioVotacion").val("");
        alert("Error: imagen muy grande o tiene una resolucion muy alta");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("usuarioVotacion", file);
        fd.append("tipo_archivo", "usuarioVotacion");
        fd.append("id_usuario", $("#id").val());
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
              $("#usuarioVotacion").val("");
              $("#lblVotacion").html(
                'Cartificado de votación: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=usuarioVotacion" target="new">ver/descargar</a>'
              );
            } else {
              btnCer.ladda("stop");
              $("#usuarioVotacion").val("");
            }
          },
        });
      }
    }
  });
  $("#btnGuardarBodega").click(function () {
    mensajes("question", $("#pregunta").val());
  });
  $("#usuarioCedula").change(function (e) {
    if (($("#usuarioCedula").val().length = 0)) {
      btnCed.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("usuarioCedula");
      var file = inputFileImage.files[0];
      var fileSize = $("#usuarioCedula")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnCed.ladda("stop");
        $("#usuarioCedula").val("");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("usuarioCedula", file);
        fd.append("tipo_archivo", "usuarioCedula");
        fd.append("id_usuario", $("#id").val());
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
              $("#usuarioCedula").val("");
              $("#lblCedula").html(
                'Cedula: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=usuarioCedula" target="new">ver/descargar</a>'
              );
            } else {
              btnCed.ladda("stop");
              $("#usuarioCedula").val("");
            }
          },
        });
      }
    }
  });
  $("#usuarioVotacion").change(function (e) {
    if (($("#usuarioVotacion").val().length = 0)) {
      btnCer.ladda("stop");
    } else {
      var inputFileImage = document.getElementById("usuarioVotacion");
      var file = inputFileImage.files[0];
      var fileSize = $("#usuarioVotacion")[0].files[0].size;
      var siezekiloByte = parseInt(fileSize / 1024);
      if (siezekiloByte > 1072) {
        btnCer.ladda("stop");
        $("#usuarioVotacion").val("");
        alert("Error: imagen muy grande o tiene una resolucion muy alta");
        mensajes("error", "Archivo muy grande o tiene una resolucion muy alta");
      } else {
        var fd = new FormData();
        fd.append("usuarioVotacion", file);
        fd.append("tipo_archivo", "usuarioVotacion");
        fd.append("id_usuario", $("#id").val());
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
              $("#usuarioVotacion").val("");
              $("#lblVotacion").html(
                'Cartificado de votación: <a href="' +
                $("#base_url").val() +
                "/v3/utilidades/viewPdf?id=" +
                $("#id").val() +
                '&t=usuarioVotacion" target="new">ver/descargar</a>'
              );
            } else {
              btnCer.ladda("stop");
              $("#usuarioVotacion").val("");
            }
          },
        });
      }
    }
  });
});
function validarFirma() {
  if ($("clave_firma").val() == "") {
    return mensajes("warning", "Debe ingresar la clave de firma...");
  } else {
    $.ajax({
      url: "v3/utilidades/validarFirma",
      type: "post",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      data: {
        clave: $("#clave_firma").val(),
        firma: $("#ruta_firma").val(),
      },
      dataType: "json",
      success: function (json) {
        mensajes(json.status, json.message);
      },
    });
  }
}
function agregarMenu() {
  limpiarModal();
  $("#id_menu").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fas fa-plus-square"></i> Nuevo ' + $("#submodulo").val()
  );
  $("#myModal").modal("toggle");
}
function agregarCatalogo() {
  limpiarModal();
  $("#id_catalogo").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fas fa-plus-square"></i> Nuevo ' + $("#submodulo").val()
  );
  $("#myModal").modal("toggle");
}
function agregarPlanes() {
  limpiarModal();
  $("#nombre").val();
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i> Nuevo ' + $("#submodulo").val());
  $("#myModal").modal("toggle");
}
async function nombreExiste(nombre) {
  let resultado = await $.ajax({
    url: "v3/" + $("#controlador").val() + "/validarnombreplan",
    method: "POST",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: { nombre: nombre },
    dataType: "json",
    success: function (response) {
      if (response.status) {
        console.log("entro");
        return true;
      } else {
        console.log("entro1");
        return false;
      }
    },
    error: function () {
    }
  });
  return resultado;
}
function agregarSubMenu() {
  limpiarModal();
  $("#id_menu").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  //$('#titulo_modal').html('<i class="fas fa-plus-square"></i> Nuevo ' + $('#submodulo').val() + nombre_menu_submenu);
  $("#myModal").modal("toggle");
}
function verMenu(id) {
  limpiarModal();
  $("#titulo_modal").html(
    '<i class="fas fa-edit"></i> Editar ' + $("#submodulo").val()
  );
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getMenu",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#id_menu").val(json.id_menu);
      $("#nombre_menu").val(json.nombre_menu);
      $("#controlador_menu").val(json.controlador_menu);
      $("#orden_menu").val(json.orden_menu);
      $("#icon_menu").val(json.icon_menu);
      if (json.estado_menu == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      $("#myModal").modal("toggle");
    },
    error() {
      window.open("v3/admin", "_self");
    },
  });
}
function subMenu(id) {
  window.location.href = "v3/" + $("#controlador").val() + "/subMenu?id=" + id;
}
function verSubmenu(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getSubMenu",
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
        '<i class="fas fa-edit"></i> Editar ' +
        $("#submodulo").val() +
        "/" +
        json.nombre_menu
      );
      $("#id_menu").val(json.id_menu);
      $("#id_menu_pertenece").val(json.id_pertenece_menu);
      $("#nombre_menu").val(json.nombre_submenu);
      $("#funcion_menu").val(json.funcion_menu);
      $("#orden_menu").val(json.orden_menu);
      $("#icon_menu").val(json.icon_menu);
      if (json.estado_menu == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      $("#myModal").modal("toggle");
    },
    error() {
      window.open("v3/admin", "_self");
    },
  });
}
function verEmpresa(id) {
  limpiarModal();
  $("#titulo_modal").html('<i class="fas fa-edit"></i> Editar ' + $("#submodulo_").val());
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getEmpresa",
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
      $("#razonsocial").val(json.razonsocial);
      $("#nombrecomercial").val(json.nombrecomercial);
      $("#email").val(json.email);
      $("#telefono1").val(json.telefono1);
      $("#telefono2").val(json.telefono2);
      $("#direccion").val(json.direccion);
      $("#web").val(json.web);
      $("#nombre_corto").val(json.nombre_corto);
      $("#urbanizacion").val(json.urbanizacion);
      $("#etapa").val(json.etapa);
      $("#manzana").val(json.manzana);
      $("#villa").val(json.villa);
      $("#referencia").val(json.referencia);
      $("#num_registro_contador").val(json.num_registro_contador);
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      idPais = json.id_pais_ciudad;
      idProvincia = json.id_provincia_ciudad;
      idCiudad = json.id_ciudad;
      $("#pais").val(idPais).trigger("change");
      $("#tipo_contribuyente").val(json.tipo_contribuyente).trigger("change");
      $("#agente_retencion").val(json.agente_retencion).trigger("change");
      $("#tipo_rimpe").val(json.tipo_rimpe).trigger("change");
      $("#ambiente").val(json.ambiente).trigger("change");
      $("#contabilidad").val(json.contabilidad).trigger("change");
      $("#contribuyente_especial").val(json.contribuyente_especial);
      $("#num_registro_contador").val(json.num_registro_contador);
      $("#id_representante").val(json.id_representante);
      $("#id_contador").val(json.id_contador);
      $("#ruta_firma").val(json.ruta_firma);
      if (json.ruta_firma != "" && json.ruta_firma != null) {
        $("#lblEntidadFirma").html(
          '<input type="file" name="entidadFirma" id="entidadFirma" style="display:none"><i class="fa-regular fa-thumbs-up"></i> Añadir archivo'
        );
        $("#lblEntidadFirma").removeClass("btn-primary");
        $("#lblEntidadFirma").addClass("btn-success");
      }
      $("#clave_firma").val(json.clave_firma);
      $("#myModal").modal("toggle");
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/getIdRepresentante",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.id_representante,
        },
        dataType: "html",
        success: function (html) {
          $("#id_representante").append(html).trigger("change");
        },
      });
      $.ajax({
        url: "v3/" + $("#controlador").val() + "/getIdRepresentante",
        type: "post",
        headers: {
          "X-CSRF-TOKEN": $("#token").val(),
        },
        data: {
          id: json.id_contador,
        },
        dataType: "html",
        success: function (html) {
          $("#id_contador").append(html).trigger("change");
        },
      });
      $("#tableInfoTributaria").dataTable().fnClearTable();
      $("#tableInfoTributaria").dataTable().fnDestroy();
      $("#tableInfoTributaria").DataTable({
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
        dom: '<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
        ajax: {
          url: "v3/" + $("#controlador").val() + "/infoTributarioEmpresa",
          type: "post",
          headers: {
            "X-CSRF-TOKEN": $("#token").val(),
          },
          data: {
            id: id,
          },
          error(error) {
            console.log(error);
          },
        },
        order: [[6, "desc"]],
        columns: [
          {
            data: "tipo_contribuyente",
          },
          {
            className: "text-center",
            data: "agente_retencion",
          },
          {
            className: "text-center",
            data: "contribuyente_especial",
          },
          {
            className: "text-center",
            data: "ambiente",
          },
          {
            className: "text-center",
            data: "contabilidad",
          },
          {
            data: "tipo_rimpe",
          },
          {
            className: "text-center",
            data: "anio",
          },
        ],
      });
    },
    error(error) {
      console.log(error);
    },
  });
}
function agregarEmpresa() {
  limpiarModal();
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#titulo_modal").html(
    '<i class="fas fa-plus-square"></i> Nuevo ' + $("#submodulo").val()
  );
  $("#myModal").modal("toggle");
}
function verPerfilUsuario(id) {
  window.open("v3/" + $("#controlador").val() + "/perfilUsuario?id=" + id, "_blank");
}
function agregarRol(id_rol) {
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i> Nuevo rol de usuario');
  $("#tableRolesUsuarios").dataTable().fnClearTable();
  $("#tableRolesUsuarios").dataTable().fnDestroy();
  $("#tableRolesUsuarios").dataTable({
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
    iDisplayLength: 500,
    dom: '<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
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
      url: "v3/" + $("#controlador").val() + "/rolesUsuariosjs",
      type: "post",
      data: {
        id: id_rol,
      },
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
    },
    columns: [
      {
        data: "nombre_menu",
      },
      {
        data: "nombre_submenu",
      },
      {
        data: "id_menu",
        className: "text-center",
        render: function (data, type, row) {
          let checked = row.crear !== undefined ? "checked" : "";
          var est = "";
          var che = '<div class="i-checks"><label><input type="checkbox" ' + checked + '  onchange="guardarGestionRol(this,\'' + row.id_menu + '\')"; id="crear_' + row.id_submenu + "|" + row.id_menu + '" name="crear_' + row.id_submenu + "|" + row.id_menu + '" ' + est + " /><i></i></label></div>";
          return che;
        },
      },
      {
        data: "id_menu",
        className: "text-center",
        render: function (data, type, row) {
          var est = "";
          let checked = row.actualizar !== undefined ? "checked" : "";
          var che = '<div class="i-checks"><label><input type="checkbox" ' + checked + ' onchange="guardarGestionRol(this,\'' + row.id_menu + '\')"; id="actualizar_' + row.id_submenu + "|" + row.id_menu + '" name="actualizar_' + row.id_submenu + "|" + row.id_menu + '" ' + est + " /><i></i></label></div>";
          return che;
        },
      },
      {
        data: "id_menu",
        className: "text-center",
        render: function (data, type, row) {
          var est = "";
          let checked = row.leer !== undefined ? "checked" : "";
          var che = '<div class="i-checks"><label><input type="checkbox" ' + checked + '  onchange="guardarGestionRol(this,\'' + row.id_menu + '\')"; id="leer_' + row.id_submenu + "|" + row.id_menu + '" name="leer_' + row.id_submenu + "|" + row.id_menu + '" ' + est + " /><i></i></label></div>";
          return che;
        },
      },
    ],
  });
  $("#myModal").modal("toggle");
}
function veCatalogo(id) {
  limpiarModal();
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getCatalogo",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#titulo_modal").html('<i class="fas fa-edit"></i> Editar ' + $("#submodulo").val() + "/" + json.catalogo);
      $("#id_catalogo").val(json.id_catalogo);
      $("#catalogo").val(json.catalogo);
      $("#valor").val(json.valor);
      if (json.estado_catalogo == 1) {
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
function verSubCatalogo(id) {
  window.location.href = "v3/" + $("#controlador").val() + "/subCatalogo?id=" + id;
}
function verPlan(id) {
  limpiarModal();
  var data = {
    id: id
  };
  var json = ajax("v3/" + $("#controlador").val() + "/getPlan", data, 'json');
  if (json.responseJSON !== null) {
    json = json.responseJSON;
    $("#titulo_modal_planes").html('<i class="fas fa-edit"></i> Editar ' + $("#submodulo").val() + "/" + json.nombre);
    $("#id").val(json.id);
    $("#nombre").val(json.nombre);
    $("#valor").val(json.valor);
    $("#tiempo").val(json.tiempo);
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
function verAuditoria(id) {
  limpiarModal();
  document.getElementById("map").style.width = "0%";
  document.getElementById("map").style.height = "0px";
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getAuditoria",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#titulo_modal").html('<i class="fas fa-edit"></i> Auditoria #: ' + json.id_auditoria + " | " + json.created_at_auditoria);
      $("#id_auditoria").val(json.id_auditoria);
      $("#controlador_").val(json.controlador_auditoria);
      $("#modelo").val(json.modelo_auditoria);
      $("#submodulo").val(json.submodulo_auditoria);
      $("#vista").val(json.vista_auditoria);
      $("#ip").val(json.ip_auditoria);
      $("#sistema").val(json.sistema_auditoria);
      $("#navegador").val(json.navegador_auditoria);
      $("#version").val(json.version_navegador_auditoria);
      $("#tiempo").val(json.tiempo_consulta_auditoria);
      $("#usuario").val(json.nombre1 + " " + json.apellido1);
      $("#fecha").val(json.created_at_auditoria);
      $("#observacion").val(json.observacion_auditoria);
      $("#latitud").val(json.latitud_auditoria);
      $("#longitud").val(json.longitud_auditoria);
      $("#query").val(json.query_auditoria);
      $("#parametros").val(json.parametros_auditoria);
      $("#obj").val(json.obj_auditoria);
      $("#myModal").modal("toggle");
    },
    error(error) {
      console.log(error);
    },
  });
}
function validarNombreRol() {
  if ($("#nombre_rol").val() != "") {
    $.ajax({
      url: "v3/" + $("#controlador").val() + "/validarNombreRol",
      type: "post",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
      data: {
        nombre: $("#nombre_rol").val(),
      },
      dataType: "json",
      success: function (json) {
        if (json != "") {
          if (json.status == "error") {
            mensajes("warning", json.message);
          }
        }
      },
    });
  }
}
function guardarGestionRol(submenu, menu) {
  if ($("#id_rol").val != "") {
    console.log(submenu.id, menu);
  }
}
function agregarUsuario() {
  limpiarModal();
  $("#edit_user").val("");
  $("#id").val("");
  $("#cheestado").attr("checked", true);
  $("#estado").val(1);
  $("#chenewsystem").attr("checked", false);
  $("#new_system").val(0);
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i>Editar Usuario');
  $("#myModal").modal("toggle");
}
function editarRolUsuario(rol) {
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i> Modificar rol de usuario');
  $("#tableRolesUsuarios").dataTable().fnClearTable();
  $("#tableRolesUsuarios").dataTable().fnDestroy();
  $("#tableRolesUsuarios").dataTable({
    language: {
      decimal: "",
      emptyTable: $("#emptyTable").val(),
      info: $("#mostrando").val() + " START " + $("#a").val() + " END " + $("#de").val() + " TOTAL " + $("#entradas").val(),
      infoEmpty: $("#mostrando").val() + " 0 " + $("#a").val() + " 0 " + $("#de").val() + " 0 " + $("#entradas").val(),
      infoFiltered: "(" + $("#filtrado").val() + " " + $("#de").val() + " MAX total " + $("#entradas").val() + ")",
      infoPostFix: "",
      thousands: ",",
      lengthMenu: $("#mostrar").val() + " MENU " + $("#entradas").val(),
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
    iDisplayLength: 500,
    dom: '<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
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
      url: "v3/" + $("#controlador").val() + "/rolesUsuariosjs",
      data: {
        rol: rol
      },
      type: "post",
      headers: {
        "X-CSRF-TOKEN": $("#token").val(),
      },
    },
    columns: [
      {
        data: "nombre_menu",
      },
      {
        data: "nombre_submenu",
      },
      {
        data: "id_menu",
        className: "text-center",
        render: function (data, type, row) {
          let checked = row.crear !== undefined ? "checked" : "";
          var checrear = "";
          var est = "";
          var che = '<div class="i-checks"><label><input type="checkbox" ' + checked + ' onchange="guardarGestionRol(this,\'' + row.id_menu + '\')"; id="crear_' + row.id_submenu + "|" + row.id_menu + '" name="crear_' + row.id_submenu + "|" + row.id_menu + '" ' + est + " /><i></i></label></div>";
          return che;
        },
      },
      {
        data: "id_menu",
        className: "text-center",
        render: function (data, type, row) {
          var est = "";
          let checked = row.actualizar !== undefined ? "checked" : "";
          var che = '<div class="i-checks"><label><input type="checkbox" ' + checked + ' onchange="guardarGestionRol(this,\'' + row.id_menu + '\')"; id="actualizar_' + row.id_submenu + "|" + row.id_menu + '" name="actualizar_' + row.id_submenu + "|" + row.id_menu + '" ' + est + " /><i></i></label></div>";
          return che;
        },
      },
      {
        data: "id_menu",
        className: "text-center",
        render: function (data, type, row) {
          var est = "";
          let checked = row.leer !== undefined ? "checked" : "";
          if (row.nombre_rol !== "") {
            $("#nombre_rol").val(row.nombre_rol);
            $("#id_rol").val(rol);
          }
          var che = '<div class="i-checks"><label><input type="checkbox" ' + checked + ' onchange="guardarGestionRol(this,\'' + row.id_menu + '\')"; id="leer_' + row.id_submenu + "|" + row.id_menu + '" name="leer_' + row.id_submenu + "|" + row.id_menu + '" ' + est + " /><i></i></label></div>";
          return che;
        },
      },
    ],
  });
  $("#myModal").modal("toggle");
}
function verUsuario(id) {
  limpiarModal();
  $("#titulo_modal").html('<i class="fas fa-edit"></i> Editar ' + $("#submodulo_").val());
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getUsuarios",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      id: id,
    },
    dataType: "json",
    success: function (json) {
      $("#edit_user").val(1);
      idPais = json.id_pais;
      idProvincia = json.id_provincia_ciudad;
      idCiudad = json.id_ciudad_usuario;
      $("#pais").val(idPais).trigger("change");
      $("#nombre1").val(json.nombre1);
      $("#nombre2").val(json.nombre2);
      $("#apellido1").val(json.apellido1);
      $("#apellido2").val(json.apellido2);
      $("#id").val(json.id);
      $("#id_pais").val(json.id_pais).trigger("change");
      $("#direccion").val(json.direccion);
      $("#telefono1").val(json.telefono1);
      $("#telefono2").val(json.telefono2);
      $("#ocupacion").val(json.ocupacion);
      $("#fecha_nacimiento").val(json.fecha_nacimiento);
      $("#id_tipo_usuario").val(json.id_tipo_usuario).trigger("change");
      $("#email").val(json.email);
      $("#urbanizacion").val(json.urbanizacion);
      $("#etapa").val(json.etapa);
      $("#manzana").val(json.manzana);
      $("#villa").val(json.villa);
      $("#referencia").val(json.referencia_direccion);
      $("#especialidad").val(json.especialidad).trigger("change");
      $("#color").val(json.color);
      if (json.ruta_cedula !== null) {
        $("#lblCedula").html('Cedula: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=usuarioCedula" target="new">ver/descargar</a>');
      }
      else {
        $("#lblCedula").html("Cedula");
      }
      if (json.ruta_certificado !== null)
        $("#lblVotacion").html('Certificado de votación: <a href="' + $("#base_url").val() + "/v3/utilidades/viewPdf?id=" + $("#id").val() + '&t=usuarioVotacion" target="new">ver/descargar</a>');
      else $("#lblVotacion").html("Certificado de votación");
      if (json.estado == 1) {
        $("#cheestado").attr("checked", true);
        $("#estado").val(1);
      } else {
        $("#cheestado").attr("checked", false);
        $("#estado").val(0);
      }
      if (json.new_system == 1) {
        $("#chenewsystem").attr("checked", true);
        $("#new_system").val(1);
      } else {
        $("#chenewsystem").attr("checked", false);
        $("#new_system").val(0);
      }
      if (json.imagen_url !== null && json.imagen_url !== '') {
        //$('#logousuario').attr('src', '../storage/app/avatars/' + json.imagen_url);
        $('#logousuario').html('<img src="avatars/' + json.imagen_url + ' />');
      }
      else {
        $('#logousuario').html('<i class="fas fa-user-tie fa-10x" style="color:#91B2F3;"></i>');
      }
      $("#myModal").modal("toggle");
    },
    error() {
      // window.open("v3/admin", "_self");
    },
  });
}
function agregarBodega() {
  limpiarModal();
  $("#id").val("");
  $("#estado").val(1);
  $("#titulo_modal").html('<i class="fas fa-plus-square"></i>Crear Bodega');
  $("#myModal").modal("toggle");
}
function mostrarImg(url) {
  limpiarModal();
  $("#titulo_modal_img").html('<i class="fa fa-picture-o"></i> Imagen ' + $("#submodulo_").val());
  $("#url_logo").attr("src", url);
  $("#modalImg").modal("toggle");
}
function validarCorreo() {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/verificarCorreo",
    type: "post",
    headers: {
      "X-CSRF-TOKEN": $("#token").val(),
    },
    data: {
      email: $("#email").val(),
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
async function guardarNuevo() {
  try {

    if ($("#tiempo").val() == "") {
      return mensajes("warning", "Debe ingresar el Tiempo...");
    } else if ($("#valor").val() == "") {
      return mensajes("warning", "Debe ingresar el valor...");
    }


    let vali = await nombreExiste($("#nombre").val());
    console.log(vali);
    if (vali.status && $("#id").val() == '') {
      alert("El nombre ya existe. Por favor, elige otro.");
      return;
    } else {
      mensajes("question", $("#pregunta").val());
    }
  } catch (error) {
    console.error("Error al verificar el nombre:", error);

  }
}