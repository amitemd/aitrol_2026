$(document).ready(function () {
  try {
    $("#tableFormularios").DataTable({
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
        },        {
            data: "numero",
          },
          {
            data: "razonsocial",
          },
          {
            data: "observacion",
          },
          {
            data: "fecha",
          },
          {
            data: "secuencia_factura",
          },
          {
            data: "serie",
          },
          {
            data: "id_usuariocrea",
          },
          {
            data: "id",
          },
          {
            className: "text-center",
            data: "id",
            render: function (data, type, row) {
              var accion = '<i class="fas fa-check-circle" style="color:green;"></i> ' + $("#activar").val();
              if (row.estado == 1) {
                accion = '<i class="fas fa-times-circle" style="color:red;"></i> ' + $("#desactivar").val();
              }
              var botones = '<div class="btn-group"><button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button><button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown"><span class="sr-only">Toggle Dropdown</span></button><div class="dropdown-menu" role="menu"><a class="dropdown-item" href="javascript:;" onclick="verProducto(\'' + row.codigo + '\');"><i class="fas fa-eye" style="color:green;"></i> ' + $('#ver').val() + '</a><a class="dropdown-item" href="javascript:;" onclick="verMovimiento(\'' + row.codigo + '\');"><i class="fas fa-people-carry" style="color:blue;"></i> Movimiento</a><a class="dropdown-item" href="javascript:;" onclick="verMovimiento(\'' + row.codigo + '\');"><i class="fa-solid fa-car"></i> Movimientoghyuufyfrtyfrt</a><div class="dropdown-divider"></div><a class="dropdown-item" href="javascript:;" onclick="cambiarEstado(\'' + row.id + "','" + row.estado + "','Factura');\">" + accion + "</a></div></div>";
              return botones;
            },
          },
      ],
    });
  } catch (error) {}
});
