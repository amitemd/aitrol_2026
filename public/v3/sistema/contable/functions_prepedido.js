var fila = 0;

$(document).ready(function () {
    $('#elementTour').val('hero-contenido,hero-accion,hero-agregar');

    try {
      $("#tableListadoPrePedidos").DataTable({
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
          { extend: "copy",  text: '<i class="fa fa-copy" title="Copy"></i>' },
          { extend: "excel", title: tituloExcel, text: '<i class="fas fa-file-excel" title="Excel"></i>' },
          { extend: "pdf",   title: tituloExcel, text: '<i class="fas fa-file-pdf" title="PDF"></i>' },
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
          { // Fecha de pedido
            className: "text-center",
            data: "fecha",
            render: function (data, type, row) {
              return row.fecha ? row.fecha : "";
            },
          },
          { // Número de pedido
            className: "text-center",
            data: "pedido",
            render: function (data, type, row) {
              return row.pedido + " / " + row.factura;
            },

          },
          { // Proveedor (nombre)
            data: "proveedor_nombre",
          },
          { // Observación
            data: "observaciones"
          },
          { // Observación
            data: "usuario"
          },
          { // Acciones
            className: "text-center",
            data: "id",
            render: function (data, type, row) {
              var botones =
                '<div class="btn-group hero-accion">'
                  + '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' btn-sm" data-toggle="dropdown"><i class="fas fa-list-ol"></i></button>'
                  + '<button type="button" class="btn btn-' + $("#colorBoton").val() + ' dropdown-toggle dropdown-icon" data-toggle="dropdown">'
                  +   '<span class="sr-only">Toggle Dropdown</span>'
                  + '</button>'
                  + '<div class="dropdown-menu" role="menu">'
                  +   '<a class="dropdown-item" href="javascript:;" onclick="verPrepedido(\'' + row.id + '\');"><i class="fas fa-eye" style="color: green;"></i> ' + $("#ver").val() + '</a>'
                  + '</div>'
                + '</div>';
              return botones;
            },
          },
        ],
      });
    } catch (error) {
      
    }

    $("#id_proveedor").select2({
        width: "element",
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

    $('#id_producto_prepedido').select2({
        width: "element",
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
            url: "v3/" + $("#controlador").val() + "/buscarProductosPrepedido",
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

    $("#btnguardar").on("click", function () {
      const valForm = validarForm();
      if (valForm.length == 0) {
        this.disabled = true;
        mensajes("question", $("#pregunta").val(), "frm_", "action", "");
      } else {
        mensajes("warning", formatearErrores(valForm));
      }
    });

    $(function () {
      if ($('#tbodyProductos_').length) {
        initDetallePrepedidoEditar();
      }
    });
});

function suma_totales() {
  var subtotalIVA = 0;
  var subtotal0   = 0;
  var ivaTotal    = 0;
  var descTotal   = 0;
  const IVA_PORC = getIvaPct();
  $('#tbodyProductos_ tr').each(function () {
    var $tr = $(this);

    var c  = parseFloat($tr.find('input[id^="cantidad"]').val() || '0');
    var p  = parseFloat($tr.find('input[id^="precio_unitario"]').val() || '0');
    var d  = parseFloat($tr.find('input[id^="descuento"]').val() || '0'); // %
    var iv = $tr.find('input[id^="iva"]').is(':checked');

    var base   = c * p;
    var desc   = base * (isNaN(d) ? 0 : d) / 100;
    var final  = base - desc;

    // asegura que la celda final muestre el valor correcto
    var $pf = $tr.find('input[id^="precio_final"]');
    if ($pf.length) $pf.val(isFinite(final) ? final.toFixed(2) : '0.00');

    descTotal += desc;
    if (iv) subtotalIVA += final; else subtotal0 += final;
  });

  ivaTotal = subtotalIVA * IVA_PORC;
  var total = subtotalIVA + subtotal0 + ivaTotal;

  // pintar en footer
  $('#subtotal_12_1').val(subtotalIVA.toFixed(2));
  $('#subtotal_0_1').val(subtotal0.toFixed(2));
  $('#descuento_1').val(descTotal.toFixed(2));
  $('#iva_1').val(ivaTotal.toFixed(2));
  $('#total_1').val(total.toFixed(2));

  $('#subtotal_12').val(subtotalIVA.toFixed(2));
  $('#subtotal_0').val(subtotal0.toFixed(2));
  $('#descuento').val(descTotal.toFixed(2));
  $('#iva').val(ivaTotal.toFixed(2));
  $('#total').val(total.toFixed(2));
}

function recalcPrecioFinal(rowId) {
  var c = parseFloat($('#cantidad' + rowId).val() || '0');
  var p = parseFloat($('#precio_unitario' + rowId).val() || '0');
  var d = parseFloat($('#descuentop' + rowId).val() || '0');
  var porc_d = d / 100;
  var pf = (c * p).toFixed(2);
  var valorDesct = pf * porc_d;
  pf = pf - valorDesct;
  $('#precio_final' + rowId).val(pf);
  suma_totales();
}

function agregarProductos() {
  var prodTxt = $('#id_producto_prepedido option:selected').text();
  if (!prodTxt && $('#id_producto_prepedido').data('select2')) {
    var p = $('#id_producto_prepedido').select2('data');
    if (p && p.length) prodTxt = p[0].text;
  }
  var partesTxt  = (prodTxt || '').split('|');
  var codigoProd = (partesTxt[0] || '').trim();
  var nombreProd = (partesTxt[1] || '').trim();

  var _prodVal = $('#id_producto_prepedido').val() || '';
  if ($('#id_producto_prepedido').data('select2')) {
    var _s2 = $('#id_producto_prepedido').select2('data');
    if (_s2 && _s2.length) _prodVal = _s2[0].id || _prodVal;
  }
  if (!_prodVal || !prodTxt || /seleccion/i.test(prodTxt)) {
    mensajes('warning', 'Seleccione un producto para continuar...');
    return;
  }

  var item = null, raw = '';
  if ($('#id_producto_prepedido').data('select2')) {
    var d = $('#id_producto_prepedido').select2('data');
    item = (d && d.length) ? d[0] : null;
    raw = item ? (item.id || '') : '';
  } else {
    raw = $('#id_producto_prepedido').val() || '';
  }

  var partesId  = (raw || '').toString().split('|');
  var ivaProd   = (partesId.length > 3 ? (partesId[3] || '0') : '0');
  var codigoSiempre = (item && typeof item.codigo_siempre !== 'undefined') ? String(item.codigo_siempre) : '0';
  var registroSan   = (item && typeof item.registro_sanitario !== 'undefined') ? item.registro_sanitario : '';

  var cantidad  = $('#cantidad').val() || '1';
  var lote      = $('#lote').val() || '';
  var fechaCad  = $('#fecha_caducidad').val() || '';
  var descuento = $('#descuento_f').val() || '0';
  var precio    = $('#precio_f').val() || '';
  var $bod = $('#bodega_principal');
  var bodVal = '';
  var bodTxt = '';
  
  var rawBod = $('#bodega_principal').val();
  if (rawBod) {
    var objBod = JSON.parse(rawBod);
    bodTxt = (objBod && objBod.nombre) ? objBod.nombre : '';
    bodVal = (objBod && objBod.id) ? objBod.id : '';
  }
  var n = parseInt(cantidad, 10) || 1;
  var repeticiones = (codigoSiempre === '1') ? Math.max(1, n) : 1;

  for (var k = 0; k < repeticiones; k++) {
    fila++;
    var idx = fila;
    var f = new Date();
    var dia = ('0' + (f.getDate())).slice(-2);
    var mes = ('0' + (f.getMonth() + 1)).slice(-2);
    var segundosMs = ('0' + (f.getMilliseconds())).slice(-1);
    var id2 = ('' + idx).slice(-1);
    var serie = f.getFullYear().toString()
               + mes + dia
               + f.getHours().toString()
               + f.getMinutes().toString()
               + f.getSeconds().toString()
               + segundosMs + id2;

    var readonlyAttr = (codigoSiempre === '1') ? 'readonly' : '';
    var minAttr      = (codigoSiempre === '1') ? '' : 'min="1"';
    var cantFila     = (codigoSiempre === '1') ? '1' : cantidad;
    var checkedIva   = (ivaProd === '1') ? 'checked' : '';
    
    var filas = `
      <tr id="tr_${idx}">
        <td><input type="text" class="form-control" id="codigo${idx}" name="codigo[]" value="${codigoProd}"></td>
        <td><input type="text" class="form-control" id="nombre${idx}" name="nombre[]" value="${nombreProd}"></td>
        <td><input type="number" ${minAttr} class="form-control text-right" id="cantidad${idx}" name="cantidad[]" value="${cantFila}" ${readonlyAttr} onkeyup="recalcPrecioFinal(${idx})" onchange="recalcPrecioFinal(${idx})"></td>
        <td><input type="text" class="form-control" id="serie${idx}" name="serie[]" value="${serie}"></td>
        <td><input type="hidden" id="bodega${idx}" name="bodega[]" value="${bodVal}"><input type="text" class="form-control" value="${(bodTxt || '').replace(/"/g,'&quot;')}" readonly></td>
        <td><input type="text" class="form-control" id="lote${idx}" name="lote[]" value="${lote}"></td>
        <td><input type="text" class="form-control" id="registro_sanitario${idx}" name="registro_sanitario[]" value="${registroSan}"></td>
        <td><input type="date" class="form-control" id="fecha_caducidad${idx}" name="fecha_caducidad[]" value="${fechaCad}"></td>
        <td><input type="number" min="0" class="form-control text-right" id="descuentop${idx}" name="descuentop[]" value="${descuento}" onkeyup="recalcPrecioFinal(${idx})" onchange="recalcPrecioFinal(${idx})"></td>
        <td><input type="number" min="0" class="form-control text-right" id="precio_unitario${idx}" name="precio_unitario[]" value="${precio}" onkeyup="recalcPrecioFinal(${idx})" onchange="recalcPrecioFinal(${idx})"></td>
        <td><input type="text" class="form-control text-right" id="precio_final${idx}" name="precio_final[]" value="0.00" readonly></td>
        <td class="text-center"><input type="checkbox" id="iva${idx}" name="iva[]" value="1" ${checkedIva} disabled></td>
        <td>
          <a href="javascript:;" class="btn btn-danger" onclick="eliminarProducto('${idx}');">
            <i class="fa fa-trash-alt"></i>
          </a>
        </td>
      </tr>`;
    $("#tbodyProductos_").append(filas);
    recalcPrecioFinal(idx);
  }
  
  resetFiltersProduct();
}

function resetFiltersProduct(){
  $('#id_producto_prepedido').val(null).trigger('change');
  $('#cantidad').val('1').attr('min','1');
  $('#lote').val('');
  $('#fecha_caducidad').val('');
  $('#descuento_f').val('0');
  $('#precio_f').val('0');
  $('#id_producto_prepedido').select2 ? $('#id_producto_prepedido').select2('open') : $('#id_producto_prepedido').focus();
}

function eliminarProducto(id) {
    $("#tr_" + id).remove();
    suma_totales();
}

function getIvaPct() {
  return parseFloat($('#impuestoIva').val() || '0'); // p.ej. 12, 15, etc.
}

const formatearErrores = (arr) =>
  "Ingrese información del campo:\n" + arr.map((e,i) => `• ${e}`).join("\n");

const validarForm = () => {
const faltan = [];

  // --- Cabecera (todos obligatorios)
  if (!$('#fecha').val())               faltan.push('fecha pedido');
  if (!$('#pedido').val())              faltan.push('número de pedido');
  if (!$('#factura').val())             faltan.push('número de documento');
  if (!$('#vencimiento').val())         faltan.push('fecha vencimiento');
  if (!$('#id_proveedor').val())        faltan.push('proveedor');
  if (!$('#tipo_prepedido').val())                faltan.push('tipo');

  // Bodega Recibe (hidden JSON o select)
  let bodOk = false;
  if ($('#bodega_principal').length) {
    try {
      const bod = JSON.parse($('#bodega_principal').val() || '{}');
      bodOk = !!(bod && bod.id);
    } catch (e) { bodOk = false; }
  } else if ($('#bodega').length) {
    bodOk = !!$('#bodega').val();
  }
  if (!bodOk) faltan.push('bodega recibe');

  // --- Detalle
  const $rows = $('#tbodyProductos_ tr');
  if ($rows.length === 0) {
    faltan.push('agregue productos');
  } else {
    $rows.each(function (i) {
      const fila = i + 1;
      const $r = $(this);

      const cantidad = parseFloat(($r.find('input[name="cantidad[]"]').val() || '0').replace(',', '.')) || 0;
      const precio   = parseFloat(($r.find('input[name="precio_unitario[]"]').val() || '0').replace(',', '.')) || 0;
      const lote     = ($r.find('input[name="lote[]"]').val() || '').trim();
      const cad      = ($r.find('input[name="fecha_caducidad[]"]').val() || '').trim();

      if (cantidad < 1) faltan.push(`cantidad mínima 1 (fila ${fila})`);
      if (precio   <= 0) faltan.push(`precio > 0 (fila ${fila})`);
      if (!lote)         faltan.push(`lote (fila ${fila})`);
      if (!cad)          faltan.push(`fecha caducidad (fila ${fila})`);
    });
  }

  return faltan;
};

function verPrepedido(id) {
  window.open($("#base_url").val() + "/v3/prepedido/crearPrepedido?id=" + id, "_self");
}

function initDetallePrepedidoEditar() {
  var id = $('#id').val() || getParam('id');
  if (!id) return;                 // no hay id => no es edición
  if (!$('#tbodyProductos_').length) return; // no existe el tbody => nada que hacer
  cargarDetallePrepedido(id);
}

function getParam(name) {
  var m = new RegExp('[?&]'+name+'=([^&#]*)').exec(window.location.search);
  return m ? decodeURIComponent(m[1].replace(/\+/g,' ')) : '';
}

function cargarDetallePrepedido(idPrePedido) {
  $.ajax({
    url: "v3/" + $("#controlador").val() + "/getPreMovimientos",
    type: "get",
    dataType: "json",
    headers: { "X-CSRF-TOKEN": $("#token").val() },
    data: { idPrePedido: idPrePedido },
    success: function (resp) {
      $("#tbodyProductos_").empty();
      if (!Array.isArray(resp) || resp.length === 0) { suma_totales(); return; }
      resp.forEach(function (it) { appendDetalleRow(it); });
      suma_totales();
    },
    error: function (err) { console.log(err); }
  });
}

function formatDateToInput(d) {
  if (!d) return '';
  var m = String(d).match(/^(\d{4})-(\d{2})-(\d{2})/); // "YYYY-MM-DD" o "YYYY-MM-DD HH:mm:ss"
  if (m) return m[1] + '-' + m[2] + '-' + m[3];
  try {
    var dt = new Datezzzz(d);
    if (!isNaN(dt)) {
      var mm = ('0' + (dt.getMonth()+1)).slice(-2);
      var dd = ('0' + dt.getDate()).slice(-2);
      return dt.getFullYear() + '-' + mm + '-' + dd;
    }
  } catch(e){}
  return '';
}

function appendDetalleRow(it) {
  // usa la variable global `fila` (NO redeclarar)
  fila++;
  var idx = fila;
  console.log(it);

  // ====== Código / Nombre (del join) ======
  var codigoProd = (it && it.producto_codigo) || '';
  var nombreProd = (it && it.producto_nombre) || '';

  // ====== Cabecera de línea ======
  var cantidad = (function(v){ var n=parseFloat(v); return isFinite(n)&&n>0 ? n : 1; })
                 (it && (it.cantidad ?? it.qty ?? it.cantidad_total));
  var serie   = (it && it.serie) || '';

  // Bodega: id para enviar, nombre para mostrar
  var bodegaId     = (it && it.id_bodega) || '';
  var bodegaNombre = (it && it.bodega_nombre) || '';

  var lote   = (it && it.lote) || '';
  var regSan = (it && it.registro_sanitario) || '';

  // ====== Fecha de caducidad -> YYYY-MM-DD (tolerante) ======
  var fechaCad = '';
  (function(d){
    if (d===undefined||d===null||d==='') return;
    if (typeof d === 'number') {
      var dt = new Date(d > 1e12 ? d : d*1000);
      if (!isNaN(dt)) { var mm=('0'+(dt.getMonth()+1)).slice(-2), dd=('0'+dt.getDate()).slice(-2); fechaCad = dt.getFullYear()+'-'+mm+'-'+dd; }
      return;
    }
    var s = String(d).trim();
    var m1 = s.match(/^(\d{4})[\/-](\d{2})[\/-](\d{2})/); if (m1) { fechaCad = m1[1]+'-'+m1[2]+'-'+m1[3]; return; }
    var m2 = s.match(/^(\d{2})[\/-](\d{2})[\/-](\d{4})/); if (m2) { fechaCad = m2[3]+'-'+m2[2]+'-'+m2[1]; return; }
    if (/^\d+$/.test(s)) { var n=parseInt(s,10), dt2=new Date(n>1e12?n:n*1000);
      if (!isNaN(dt2)) { var mm2=('0'+(dt2.getMonth()+1)).slice(-2), dd2=('0'+dt2.getDate()).slice(-2); fechaCad = dt2.getFullYear()+'-'+mm2+'-'+dd2; return; } }
    var dt3 = new Date(s); if (!isNaN(dt3)) { var mm3=('0'+(dt3.getMonth()+1)).slice(-2), dd3=('0'+dt3.getDate()).slice(-2); fechaCad = dt3.getFullYear()+'-'+mm3+'-'+dd3; }
  })(
    it && (
      it.fecha_caducidad ?? it.fechaCaducidad ?? it.fec_cad ?? it.caducidad ??
      it.fecha_vencimiento ?? it.fec_venc ?? it.fechacaducidad
    )
  );

  // ====== Descuento / Precio ======
  var descuento = (function(v){ var n=parseFloat(v); return isFinite(n)?n:0; })
                  (it && (it.descuentop ?? it.descuento_porcentaje ?? it.descuento));
  var precio    = (function(v){ var n=parseFloat(v); return isFinite(n)?n:0; })
                  (it && (it.precio_unitario ?? it.precio ?? it.p_unit));

  // ====== IVA (desde producto) ======
  var ivaFlag = (function(v){
    if (v === 1 || v === '1' || v === true || v === 'true' || v === 'S' || v === 's') return true;
    var f = parseFloat(v); return isFinite(f) && f > 0;
  })(it && it.producto_iva);
  console.log(it.codigo_siempre);
  var codigoSiempre = (it && typeof it.codigo_siempre !== 'undefined') ? String(it.codigo_siempre) : '0';
  var readonlyAttr = (codigoSiempre === '1') ? 'readonly' : '';
  // ====== render de fila ======
  var filas = `
    <tr id="tr_${idx}">
      <td><input type="text" class="form-control" id="codigo${idx}" name="codigo[]" value="${(String(codigoProd)).replace(/"/g,'&quot;')}"></td>
      <td><input type="text" class="form-control" id="nombre${idx}" name="nombre[]" value="${(String(nombreProd)).replace(/"/g,'&quot;')}"></td>
      <td><input type="number" min="1" class="form-control text-right" id="cantidad${idx}" name="cantidad[]" value="${cantidad}" ${readonlyAttr} onkeyup="recalcPrecioFinal(${idx})" onchange="recalcPrecioFinal(${idx})"></td>
      <td><input type="text" class="form-control" id="serie${idx}" name="serie[]" value="${(String(serie)).replace(/"/g,'&quot;')}"></td>
      <td>
        <input type="hidden" id="bodega${idx}" name="bodega[]" value="${(String(bodegaId)).replace(/"/g,'&quot;')}">
        <input type="text" class="form-control" value="${(String(bodegaNombre)).replace(/"/g,'&quot;')}" readonly>
      </td>
      <td><input type="text" class="form-control" id="lote${idx}" name="lote[]" value="${(String(lote)).replace(/"/g,'&quot;')}"></td>
      <td><input type="text" class="form-control" id="registro_sanitario${idx}" name="registro_sanitario[]" value="${(String(regSan)).replace(/"/g,'&quot;')}"></td>
      <td><input type="date" class="form-control" id="fecha_caducidad${idx}" name="fecha_caducidad[]" value="${fechaCad}"></td>
      <td><input type="number" min="0" class="form-control text-right" id="descuentop${idx}" name="descuentop[]" value="${descuento}" onkeyup="recalcPrecioFinal(${idx})" onchange="recalcPrecioFinal(${idx})"></td>
      <td><input type="number" min="0" class="form-control text-right" id="precio_unitario${idx}" name="precio_unitario[]" value="${precio}" onkeyup="recalcPrecioFinal(${idx})" onchange="recalcPrecioFinal(${idx})"></td>
      <td><input type="text" class="form-control text-right" id="precio_final${idx}" name="precio_final[]" value="0.00" readonly></td>
      <td class="text-center"><input type="checkbox" id="iva${idx}" name="iva[]" value="1" ${ivaFlag ? 'checked' : ''} disabled></td>
      <td>
        <a href="javascript:;" class="btn btn-danger" onclick="eliminarProducto('${idx}');">
          <i class="fa fa-trash-alt"></i>
        </a>
      </td>
    </tr>`;

  $("#tbodyProductos_").append(filas);
  recalcPrecioFinal(idx);
}

