
$(document).ready(function () {
  $('#elementTour').val('hero-contenido,hero-accion,hero-agregar');
  try {
    // Evita doble init
    if ($.fn.dataTable.isDataTable('#tableTraslado')) {
      $('#tableTraslado').DataTable().destroy();
    }

    // 1) Si solo existe la fila de fallback (colspan), quítala
    const $tb = $('#tableTraslado tbody');
    const hasFallback = $tb.find('tr.dt-empty-fallback').length > 0;
    const hasRealRows = $tb.find('tr[data-id]').length > 0;
    if (hasFallback && !hasRealRows) {
      $tb.empty(); // <-- Deja el tbody vacío para que DataTables pinte su "emptyTable"
    }

    // 2) Inicializa DataTables
    $('#tableTraslado').DataTable({
      language: {
        decimal: "",
        emptyTable: $('#emptyTable').val() || 'No hay información',
        info: ($('#mostrando').val() || 'Mostrando') + ' _START_ ' + ($('#a').val() || 'a') + ' _END_ ' + ($('#de').val() || 'de') + ' _TOTAL_ ' + ($('#entradas').val() || 'entradas'),
        infoEmpty: ($('#mostrando').val() || 'Mostrando') + ' 0 ' + ($('#a').val() || 'a') + ' 0 ' + ($('#de').val() || 'de') + ' 0 ' + ($('#entradas').val() || 'entradas'),
        infoFiltered: '(' + ($('#filtrado').val() || 'filtrado') + ' ' + ($('#de').val() || 'de') + ' _MAX_ total ' + ($('#entradas').val() || 'entradas') + ')',
        thousands: ",",
        lengthMenu: ($('#mostrar').val() || 'Mostrar') + ' _MENU_ ' + ($('#entradas').val() || 'entradas'),
        loadingRecords: ($('#loadingRecords').val() || 'Cargando') + '...',
        processing: ($('#processing').val() || 'Procesando') + '...',
        search: $('#search').val() || 'Buscar:',
        zeroRecords: $('#zeroRecords').val() || 'No se encontraron resultados',
        paginate: {
          first: $('#first').val() || 'Primero',
          last: $('#last').val() || 'Último',
          next: $('#next').val() || 'Siguiente',
          previous: $('#previous').val() || 'Anterior'
        }
      },
      responsive: true,
      lengthChange: false,
      autoWidth: true,
      deferRender: true,
      dom: 'B<"float-right"f>t<"float-left"l><"float-left"i><"float-right"p><"clearfix">',
      buttons: [
        { extend: 'copy', text: '<i class="fa fa-copy" title="Copy"></i>' },
        { extend: 'excel', title: (window.tituloExcel || 'Exportación'), text: '<i class="fas fa-file-excel" title="Excel"></i>' },
        { extend: 'pdf', title: (window.tituloExcel || 'Exportación'), text: '<i class="fas fa-file-pdf" title="PDF"></i>' },
        {
          extend: 'print', text: '<i class="fa fa-print" title="Print"></i>',
          customize: function (win) {
            $(win.document.body).addClass('white-bg').css('font-size', '10px');
            $(win.document.body).find('table').addClass('compact').css('font-size', 'inherit');
          }
        }
      ],

      // Columna 0: botón de detalle SOLO cuando hay data-id
      columnDefs: [{
        targets: 0,
        orderable: false,
        searchable: false,
        className: 'text-center align-middle',
        width: '40px',
        createdCell: function (td, cellData, rowData, row, col) {
          const $tr = $(td).closest('tr');

          // Si es la fila especial "sin datos" de DataTables, no tocar
          if ($tr.find('td.dataTables_empty').length) return;

          // Si no hay data-id, deja vacío
          const id = $tr.data('id');
          if (!id) { $(td).empty(); return; }

          // Inyecta el botón
          $(td).html(
            '<button style="background:none;border:none"'
            + ' onclick="toggleDetalleCollapse(' + id + ', this)" title="Detalle">'
            + '  <i id="details' + id + '" class="fa fa-chevron-circle-down" aria-hidden="true"></i>'
            + '</button>'
          );
        }
      }],

      // Reaseguro: si por cualquier cosa no hay filas, pinta el "emptyTable"
      drawCallback: function () {
        const api = this.api();
        const rows = api.rows({ page: 'current' }).data().length;
        if (rows === 0) {
          const colSpan = api.columns(':visible').count();
          const msg = $('#emptyTable').val() || 'No hay información';
          $(api.table().body()).html(
            '<tr><td class="dataTables_empty text-center" colspan="' + colSpan + '">' + msg + '</td></tr>'
          );
        }
      }
    });
  } catch (e) {
    console.log(e);
  }


});


// === DETALLE con Collapse (lazy-load, 1 sola carga) ===

function toggleDetalleCollapse(id, btn) {
  const $tr = $(btn).closest('tr');
  const $existing = $tr.next('.subrow-' + id);

  if ($existing.length) {
    const $col = $existing.find('#col-' + id);
    if ($.fn.collapse) $col.collapse('toggle'); else $col.toggle();
    return;
  }

  // Inserta la fila pero NO la muestres aún
  $tr.after(renderDetailCollapseRow(id));
  const $col = $('#col-' + id);
  const $tbody = $('#tbody-det-' + id);

  if ($.fn.collapse) $col.collapse({ toggle: false }); else $col.hide();

  $.ajax({
    type: "GET",
    url: "v3/" + $('#controlador').val() + "/detalleTraslado",
    data: { id },
    dataType: "json",
    success: function (resp) {
      const data = Array.isArray(resp) ? resp
        : (Array.isArray(resp.data) ? resp.data
          : (Array.isArray(resp.aaData) ? resp.aaData : []));
      $tbody.html(
        data.length
          ? data.map(rowToDetailTr).join('')
          : '<tr><td colspan="6" class="text-center py-2">Sin detalles</td></tr>'
      );

      // AHORA sí, mostrar
      if ($.fn.collapse) $col.collapse('show'); else $col.show();
    },
    error: function (xhr) {
      console.error('detalle error', xhr.status, xhr.statusText, xhr.responseText);
      $tbody.html('<tr><td colspan="6" class="text-center text-danger py-2">Error al cargar detalle</td></tr>');
      if ($.fn.collapse) $col.collapse('show'); else $col.show();
    }
  });
}

// Genera la fila colapsable completa
function renderDetailCollapseRow(id) {
  return (
    '<tr class="subrow subrow-' + id + '">'
    + '  <td colspan="8" class="p-0 border-0">'
    + '    <div id="col-' + id + '" class="collapse" data-parent="#tableTraslado">'
    + '      <div class="dt-detail-wrap">'
    + '        <div class="dt-detail-card">'
    + '          <div class="table-responsive mb-0">'
    + '            <table class="table-detail">'
    + '              <colgroup>'
    + '                <col style="width:18%;">'
    + '                <col style="width:42%;">'
    + '                <col style="width:12%;">'
    + '                <col style="width:9%;">'
    + '                <col style="width:9%;">'
    + '                <col style="width:10%;">'
    + '              </colgroup>'
    + '              <thead>'
    + '                <tr>'
    + '                  <th>Serie</th>'
    + '                  <th>Nombre</th>'
    + '                  <th>Lote</th>'
    + '                  <th class="num">Cantidad</th>'
    + '                  <th class="num">Cantidad de Uso</th>'
    + '                  <th class="num">Total</th>'
    + '                </tr>'
    + '              </thead>'
    + '              <tbody id="tbody-det-' + id + '">'
    + '                <tr><td colspan="6" class="text-center py-2">Cargando...</td></tr>'
    + '              </tbody>'
    + '            </table>'
    + '          </div>'
    + '        </div>'
    + '      </div>'
    + '    </div>'
    + '  </td>'
    + '</tr>'
  );
}



// Render de cada fila del detalle
function rowToDetailTr(it) {
  const serie = it.serie ?? '';
  const producto = it.producto ?? '';
  const lote = it.lote ?? '';
  const cantidad = Number(it.cantidad || 0);
  const uso = Number(it.uso || 0);
  const total = Number(it.total || 0).toFixed(2);

  return (
    '<tr>'
    + '  <td>' + escapeHtml(serie) + '</td>'
    + '  <td>' + escapeHtml(producto) + '</td>'
    + '  <td>' + escapeHtml(lote) + '</td>'
    + '  <td class="text-right">' + cantidad + '</td>'
    + '  <td class="text-right">' + uso + '</td>'
    + '  <td class="text-right">' + total + '</td>'
    + '</tr>'
  );
}

// Actualiza el icono ↑/↓ si no hay bootstrap o en eventos de collapse
function toggleIcon(id, abierto) {
  const $i = $('#details' + id);
  if (abierto) $i.removeClass('fa-chevron-circle-down').addClass('fa-chevron-circle-up');
  else $i.removeClass('fa-chevron-circle-up').addClass('fa-chevron-circle-down');
}

// Eventos de Bootstrap 4 (AdminLTE 3)
$(document).on('shown.bs.collapse', '#tableTraslado .collapse', function () {
  const id = this.id.replace('col-', '');
  toggleIcon(id, true);
});
$(document).on('hidden.bs.collapse', '#tableTraslado .collapse', function () {
  const id = this.id.replace('col-', '');
  toggleIcon(id, false);
});

// Escape básico
function escapeHtml(str) {
  return String(str).replace(/[&<>"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s]));
}

function agregarTraslado() {
  window.open("v3/" + $("#controlador").val() + "/nuevotraslado", "_self");
}
function filtrarTraslados() {
  const fd = document.getElementById('fecha_desde').value || '';
  const fh = document.getElementById('fecha_hasta').value || '';
  const url = new URL(window.location.href);
  fd ? url.searchParams.set('fecha_desde', fd) : url.searchParams.delete('fecha_desde');
  fh ? url.searchParams.set('fecha_hasta', fh) : url.searchParams.delete('fecha_hasta');
  window.location.href = url.toString();
}

// Sanitizar texto
function esc(str) { return (str ?? '').toString().replace(/[&<>"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s])); }

// Construye el HTML del panel EXACTO como tu snippet
function buildPanelHtml(item, fallbackId) {
  // item: viene del endpoint (toItemPayload)
  const codigo = item?.producto?.codigo ?? fallbackId ?? '';
  const nombre = item?.producto?.nombre ?? fallbackId ?? '';
  const label = `${esc(codigo)} | ${esc(nombre)}`;
  const serie = esc(item.serie ?? '');
  const lote = esc(item.lote ?? '');
  const vence = esc(item.fecha_vence ?? '');
  const existencia = esc(item.existencia ?? '0');
  const idProd = esc(item.id_producto ?? '');
  const cantUsos = esc(item.cantidad_usos_kardex ?? '0');
  const precio = esc(item.precio ?? '0');

  return `
      <div class="panel panel-default details" id="heading_details">
        <div class="panel-heading">
          <div class="row titulo">
            <div class="col-sm-1" style="text-align: left;"><label class="numdetalle"></label></div>
            <div class="col-md-9" style="text-align: left;">
              <label class="col-md-9">${label}</label>
            </div>
            <div class="col-md-2" style="text-align: right;">
              <button type="button" class="btn btn-danger des" onclick="$(this).closest('.panel').remove()">
                <i class="fa fa-remove"></i>
              </button>
            </div>
            <input type="hidden" class="product" value="${serie}">
          </div>
        </div>

        <div class="panel-body" style="padding:0;">
          <div class="col-md-12 table-responsive" style="padding:0 !important;">
            <table class="table table-bordered table-hover dataTable noacti" role="grid" aria-describedby="example2_info" style="margin-top:0 !important; width:100%!important;">
              <thead>
                <tr>
                  <th tabindex="0">Cantidad</th>
                  <th tabindex="0">Serie</th>
                  <th tabindex="0">Lote</th>
                  <th tabindex="0">Fecha Vence</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <input type="hidden" name="cantidad_usos_kardex[]" value="${cantUsos}">
                  <td>
                    <input type="hidden" name="id[]" value="${idProd}">
                    <input type="number" style="width:80%;height:20px; text-align:center;" class="form-control cneto" name="cantidad[]" required value="1" onchange="existenciax(this)">
                    <input type="hidden" name="existencia" class="existencia" value="${existencia}">
                  </td>
                  <td><input type="text" style="width:80%;height:20px;" class="form-control" name="serie[]" value="${serie}" readonly></td>
                  <td><input type="text" style="width:80%;height:20px;" class="form-control" name="lote[]" value="${lote}" readonly onchange="lote(this)"></td>
                  <td>
                    <input type="text" style="width:80%;height:20px;" class="form-control" name="vence[]" value="${vence}" readonly>
                    <input type="hidden" name="precio[]" value="${precio}">
                  </td>
                  <td style="text-align:right;">
                    <button class="btn btn-danger" type="button" onclick="return $(this).parent().parent().remove()">
                      <i class="fa fa-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>`;
}

// Función principal: se llama desde onchange del input (onchange="add(this)")
function add(e) {
  const bodega = $("#bodega_saliente").val();
  if (!bodega) {
    $(e).val('');
    Swal.fire('Primero seleccione la bodega');
    return;
  }
  const id = $(e).val();
  if (!id) return;

  // Si tienes una validación de duplicados:
  if (typeof valida_add === 'function' && valida_add()) {
    Swal.fire('Mensaje', 'Ya agregó un producto similar, ingrese otro', 'error');
    $("#inputserie").val("");
    return;
  }

  $.ajax({
    type: "get",

    url: "v3/" + $('#controlador').val() + "/htmlSourceJson",
    data: { id, bodega },
    dataType: "json",
    success: function (resp) {
      $(e).val('');
      if (!resp || resp.ok === false) {
        Swal.fire("Error:", resp?.message || "No se encontró existencia en bodega.", "error");
        return;
      }
      const html = buildPanelHtml(resp.item, id);
      $("#heading").append(html);

      if (typeof sumar_detalles === 'function') {
        sumar_detalles();
      }
    },
    error: function () {
      $(e).val('');
      Swal.fire("Error:", "Error al cargar item.", "error");
    }
  });
}
function elemento_envio(el, ev) {
  ev = ev || window.event;
  const key = ev.key || ev.keyCode;

  // Enter (compatible con navegadores viejos)
  if (key === 'Enter' || key === 13) {
    ev.preventDefault();       // evita submit del formulario
    ev.stopPropagation();
    add(el);                   // llama a tu función existente
  }
}
function elemento_envio_lote(el, ev) {
  ev = ev || window.event;
  const key = ev.key || ev.keyCode;
  if (key === 'Enter' || key === 13) {
    ev.preventDefault();
    add_lote(el);
  }
}

function elemento_envio_pedido(el, ev) {
  ev = ev || window.event;
  const key = ev.key || ev.keyCode;
  if (key === 'Enter' || key === 13) {
    ev.preventDefault();
    add_pedido(el);
  }
}
// Buscar por PEDIDO y renderizar
function add_pedido(e) {
  const bodega = $("#bodega_saliente").val();
  if (!bodega) {
    $(e).val(''); Swal.fire('Primero seleccione la bodega'); return;
  }

  const pedido = $(e).val();
  if (!pedido) return;

  // evita duplicados si usas validador
  if (typeof valida_add === 'function' && valida_add()) {
    Swal.fire('Mensaje', 'Ya agregó un producto similar, ingrese otro', 'error');
    $("#inputpedido").val("");
    return;
  }

  $.ajax({
    type: "get",
    url: "v3/" + $('#controlador').val() + "/htmlSourceJsonPedido",
    data: { pedido, bodega },
    dataType: "json",
    success: function (resp) {
      $(e).val('');
      if (!resp || resp.ok === false) {
        Swal.fire("Error:", resp?.message || "No se encontró información para ese pedido.", "error");
        return;
      }
      const items = resp.items || [];
      if (!items.length) {
        Swal.fire("Aviso:", "No hay pendientes para ese pedido.", "info");
        return;
      }
      let html = '';
      items.forEach((it, i) => { html += buildPanelHtmlPedido(it, pedido, i); });
      $("#heading").append(html);

      if (typeof sumar_detalles === 'function') sumar_detalles();
    },
    error: function () {
      $(e).val('');
      Swal.fire("Error:", "Error al cargar pedido.", "error");
    }
  });
}
function add_lote(e) {
  const bodega = $("#bodega_saliente").val();
  if (!bodega) {
    $(e).val('');
    Swal.fire('Primero seleccione la bodega');
    return;
  }

  const lote = $(e).val();
  if (!lote) return;

  // Si tienes validador para evitar duplicados, reúsalo aquí
  if (typeof valida_add === 'function' && valida_add()) {
    Swal.fire('Mensaje', 'Ya agregó un producto similar, ingrese otro', 'error');
    $("#inputlote").val("");
    return;
  }

  $.ajax({
    type: "get",
    url: "v3/" + $('#controlador').val() + "/htmlSourceJsonLote",
    data: { id: lote, bodega: bodega },
    dataType: "json",
    success: function (resp) {
      $(e).val('');
      if (!resp || resp.ok === false) {
        Swal.fire("Error:", resp?.message || "No se encontró existencia para ese lote.", "error");
        return;
      }
      if (!resp.items || !resp.items.length) {
        Swal.fire("Aviso:", "No hay series disponibles para ese lote.", "info");
        return;
      }

      let html = '';
      for (const it of resp.items) {
        html += buildPanelHtml(it, lote); // MISMO armado de detalle
      }
      $("#heading").append(html);

      if (typeof sumar_detalles === 'function') {
        sumar_detalles();
      }
    },
    error: function () {
      $(e).val('');
      Swal.fire("Error:", "Error al cargar lote.", "error");
    }
  });





}
function buildPanelHtmlPedido(item, pedidoNumero, key) {
  // item: { id, id_producto, serie, lote, fecha_vence, existencia, precio, cantidad_usos_kardex, pendiente, producto:{codigo,nombre}, uniq }
  const label = `${escapeHtml(item.producto?.codigo || '')} | ${escapeHtml(item.producto?.nombre || '')} | PEDIDO: ${escapeHtml(pedidoNumero)}`;
  const serie = escapeHtml(item.serie || '');
  const lote = escapeHtml(item.lote || '');
  const vence = escapeHtml(item.fecha_vence || '');
  const existencia = escapeHtml(item.existencia ?? '0');
  const idProd = escapeHtml(item.id_producto ?? '');
  const cantUsos = escapeHtml(item.cantidad_usos_kardex ?? '0');
  const pendiente = escapeHtml(item.pendiente ?? '1');
  const precio = escapeHtml(item.precio ?? '0');
  const uniq = escapeHtml(item.uniq || (key || 0)); // para IDs únicos

  return `
      <div class="panel panel-default details" id="heading_details">
        <div class="panel-heading">
          <div class="row titulo">
            <div class="col-sm-1" style="text-align: left;"><label class="numdetalle"></label></div>
            <div class="col-md-9" style="text-align: left;">
              <label class="col-md-10">${label}</label>
            </div>
            <div class="col-md-2" style="text-align: right;">
              <button type="button" class="btn btn-danger des" onclick="$(this).closest('.panel').remove()">
                <i class="fa fa-remove"></i>
              </button>
            </div>
            <input type="hidden" class="product" value="${serie}">
          </div>
        </div>

        <div class="panel-body" style="padding:0;">
          <div class="col-md-12 table-responsive" style="padding:0 !important;">
            <table class="table table-bordered table-hover dataTable noacti" role="grid" style="margin-top:0 !important; width:100%!important;">
              <thead>
                <tr>
                  <th tabindex="0">Cantidad</th>
                  <th tabindex="0">Serie</th>
                  <th tabindex="0">Lote</th>
                  <th tabindex="0">Fecha Vence</th>
                  <th>Accion</th>
                </tr>
              </thead>
              <tbody>
                <tr id="del${uniq}">
                  <input type="hidden" name="cantidad_usos_kardex[]" value="${cantUsos}">
                  <td>
                    <input type="hidden" name="id[]" value="${idProd}">
                    <input type="number" style="width:80%;height:20px; text-align:center;"
                           class="form-control cneto" name="cantidad[]" required
                           value="${pendiente}" onchange="existenciax(this)">
                    <input type="hidden" name="existencia" class="existencia" value="${existencia}">
                  </td>
                  <td><input type="text" style="width:80%;height:20px;" class="form-control" name="serie[]" value="${serie}" readonly></td>
                  <td><input type="text" style="width:80%;height:20px;" class="form-control" name="lote[]" value="${lote}" onchange="lote(this)" readonly></td>
                  <td>
                    <input type="text" style="width:80%;height:20px;" class="form-control" name="vence[]" value="${vence}" readonly>
                    <input type="hidden" name="precio[]" value="${precio}">
                  </td>
                  <td style="text-align:right;">
                    <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                      <input onclick="disabledTr('${uniq}')" type="checkbox" id="check${uniq}" class="chk-sm" checked>
                      <button class="btn btn-danger btn-sm" type="button" onclick="return $(this).parent().parent().remove()">
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>`;
}


// Función para escapar HTML
function escapeHtml(str) {
  return (str ?? '').toString().replace(/[&<>"']/g, s => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[s]));
}

// Habilitar/Deshabilitar fila (stub si no lo tienes ya)
function disabledTr(id) {
  const $tr = $('#del' + id);
  const disabled = !$('#check' + id).is(':checked');
  $tr.find('input,textarea,select,button').not('#check' + id).prop('disabled', disabled);
}
(function () {
  function getPrincipalId() {
    const el = document.getElementById('bodega_principal_id');
    if (!el) return null;
    const v = (el.value || '').trim();
    return v === '' ? null : v; // lo tratamos como string
  }

  function togglePedidoInput() {
    const principalId = getPrincipalId();           // string o null
    const selectedVal = $('#bodega_saliente').val(); // siempre string con select/select2
    const isPrincipal = principalId !== null && String(selectedVal) === String(principalId);

    const $inp = $('#inputpedido');
    if (isPrincipal) {
      $inp.prop('disabled', false).removeAttr('disabled');
    } else {
      $inp.prop('disabled', true).val('');
    }

    // Debug rápido si aún falla:
    // console.log({ principalId, selectedVal, isPrincipal, texto: $('#bodega_saliente option:selected').text() });
  }

  function bind() {
    // Si usas select2, escucha ambos eventos
    $('#bodega_saliente')
      .on('change', togglePedidoInput)
      .on('select2:select', togglePedidoInput);

    // Ejecuta una vez al cargar (tras init de select2)
    setTimeout(togglePedidoInput, 0);
  }

  // Asegúrate de que este archivo JS se incluya después de jQuery/select2
  $(document).ready(bind);
})();

function validarFormTransito() {
  const faltan = [];
  const bs = $('#bodega_saliente').val();
  const be = $('#bodega_entrante').val();

  if (!bs) faltan.push('Bodega Saliente');
  if (!be) faltan.push('Bodega Entrante');
  if (bs && be && String(bs) === String(be)) faltan.push('Bodegas distintas');

  // Debe existir al menos 1 fila de detalle habilitada
  const $rows = $('#heading .details tbody tr:has(input[name="cantidad[]"]:enabled)');
  if ($rows.length === 0) faltan.push('Detalle');

  // Cantidades > 0 en filas habilitadas
  $rows.find('input[name="cantidad[]"]').each(function () {
    const v = parseFloat($(this).val());
    if (isNaN(v) || v <= 0) {
      faltan.push('Cantidad válida');
      return false;
    }
  });

  // Campos serie/lote/vence presentes (en filas habilitadas)
  $rows.each(function () {
    const serie = $(this).find('input[name="serie[]"]').val();
    const lote = $(this).find('input[name="lote[]"]').val();
    const vence = $(this).find('input[name="vence[]"]').val();
    if (!serie) { faltan.push('Serie'); return false; }
    if (!lote) { faltan.push('Lote'); return false; }
    if (!vence) { faltan.push('Fecha Vence'); return false; }
  });

  // Quitar duplicados en los mensajes
  return Array.from(new Set(faltan));
}


function guardarTransito() {
  const valForm = validarFormTransito();

  if (valForm.length === 0) {
    const pregunta = $('#pregunta').val() || '¿Desea guardar el traslado?';

    // ⚠️ OJO: 5to parámetro en blanco => mensajes NO redirige
    mensajes('question', pregunta, 'frm_transito', 'action', '');
  } else {
    return mensajes('warning', 'Ingrese información del campo ' + valForm.join(', '));
  }
}

// --- Manejo centralizado de la respuesta de guardarTransito ---
(function () {
  // Redirigir al índice SOLO cuando el backend confirma success
  function redirectOnSuccess() {
    const ctrl = $('#controlador').val();
    if (ctrl) {
      window.location.href = 'v3/' + ctrl + '/index';
    }
  }

  // Resetear cantidades a 1 + alertar el motivo
  function handleStockError(msg) {
    // Solo filas habilitadas
    $('#heading .details tbody tr:has(input[name="cantidad[]"]:enabled)').each(function () {
      $(this).find('input[name="cantidad[]"]:enabled').val(1);
    });
    if (typeof sumar_detalles === 'function') { try { sumar_detalles(); } catch (e) { } }
    Swal.fire('Validación de stock', (msg || 'Stock insuficiente.'), 'warning');
  }

  // Success global: si fue guardarTransito y el JSON dice success => redirigimos
  $(document).ajaxSuccess(function (evt, xhr, settings) {
    if (!settings || !settings.url) return;
    if (!/\/guardarTransito($|\?)/.test(settings.url)) return;

    // Si tu backend responde JSON con {status:'success', ...}
    var json = xhr.responseJSON || {};
    if (json && json.status === 'success') {
      redirectOnSuccess();
    } else if (json && json.status === 'error') {
      // Por si llegara con 200 pero status=error (backup)
      handleStockError(json.message);
    }
  });

  // Error global: si es 422 desde guardarTransito => no rediriges, solo reseteas y avisas
  $(document).ajaxError(function (evt, jqXHR, settings) {
    if (!settings || !settings.url) return;
    if (!/\/guardarTransito($|\?)/.test(settings.url)) return;

    if (jqXHR && jqXHR.status === 422) {
      var msg = (jqXHR.responseJSON && jqXHR.responseJSON.message) || 'Stock insuficiente.';
      handleStockError(msg);
    }
  });
})();
function pdf_transito(id, est) {
  var url = "v3/" + $("#controlador").val() + "/pdf_transito?id=" + encodeURIComponent(id);
  if (est) { url += "&est=1"; } // opcional: para ocultar la columna "Total" en el PDF
  window.open(url, "_blank");
}


