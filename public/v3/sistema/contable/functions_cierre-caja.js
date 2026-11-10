(function ($) {
  'use strict';
  var cfg = window.cashClosingV3;
  if (!cfg || !document.getElementById('cash-closing-filters')) return;

  function params() { return $('#cash-closing-filters').serialize(); }
  function showError(xhr) {
    var message = 'No fue posible completar la operación. Intente nuevamente.';
    if (xhr && xhr.responseJSON) {
      message = xhr.responseJSON.message || message;
      if (xhr.responseJSON.errors) {
        var first = Object.keys(xhr.responseJSON.errors)[0];
        if (first) message = xhr.responseJSON.errors[first][0];
      }
    }
    $('#cash-closing-success').addClass('d-none');
    $('#cash-closing-error').removeClass('d-none').text(message);
  }
  function showSuccess(message) {
    $('#cash-closing-error').addClass('d-none');
    $('#cash-closing-success').removeClass('d-none').text(message);
  }
  function load(url, target, countTarget, exactUrl) {
    $(target).html('<div class="text-center p-4 text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando…</div>');
    var requestUrl = exactUrl ? url : url + (url.indexOf('?') === -1 ? '?' : '&') + params();
    return $.getJSON(requestUrl).done(function (data) {
      $(target).html(data.html); $(countTarget).text(data.count); $('#cash-closing-error').addClass('d-none');
      if (target === '#cash-parking') $('#parking-total').text('$ ' + Number(data.total || 0).toFixed(2));
    }).fail(showError);
  }
  function reload() {
    load(cfg.receipts, '#cash-receipts', '#receipt-count');
    load(cfg.pending, '#cash-pending', '#pending-count');
    load(cfg.parking, '#cash-parking', '#parking-count');
    load(cfg.cashCounts, '#cash-counts', '#cash-count-total');
  }

  $('#cash-closing-filters').on('submit', function (event) { event.preventDefault(); reload(); });
  $('[data-export]').on('click', function () {
    var form = $('#cash-closing-export').attr('action', $(this).data('export')).empty();
    form.append($('<input>', {type:'hidden', name:'_token', value:cfg.csrf}));
    $('#cash-closing-filters').serializeArray().forEach(function (field) {
      form.append($('<input>', {type:'hidden', name:field.name, value:field.value}));
    });
    form.trigger('submit');
  });
  $(document).on('click', '#cash-receipts .pagination a, #cash-pending .pagination a, #cash-parking .pagination a, #cash-counts .pagination a', function (event) {
    event.preventDefault();
    var pane = $(this).closest('.tab-pane').attr('id');
    var map = {
      'cash-receipts': [cfg.receipts, '#receipt-count'],
      'cash-pending': [cfg.pending, '#pending-count'],
      'cash-parking': [cfg.parking, '#parking-count'],
      'cash-counts': [cfg.cashCounts, '#cash-count-total']
    };
    if (map[pane]) load($(this).attr('href'), '#'+pane, map[pane][1], true);
  });

  $('#cash-closing-user').select2({theme:'bootstrap4', width:'100%', allowClear:true, minimumInputLength:0,
    placeholder:'Todos los usuarios de recepción', language:{noResults:function(){return 'No se encontraron usuarios de recepción';}, searching:function(){return 'Buscando…';}},
    ajax:{url:cfg.users, dataType:'json', delay:250, data:function(p){return {q:p.term};}, processResults:function(d){return d;}}});
  $('#cash-closing-user').on('select2:open', function () {
    window.setTimeout(function () { $('.select2-container--open .select2-search__field').first().trigger('focus'); }, 0);
  });
  function createCashCount(userScoped) {
    var cash = $('[name="caja"]').val();
    if (!cash) { showError({responseJSON:{message:'Seleccione una caja antes de crear el arqueo.'}}); return; }
    var button = $(userScoped ? '#cash-count-user' : '#cash-count-create');
    button.prop('disabled', true);
    $.ajax({url:userScoped ? cfg.createUserCashCount : cfg.createCashCount, type:'POST', data:params()+'&_token='+encodeURIComponent(cfg.csrf)})
      .done(function(data){ showSuccess(data.message); window.location.href=cfg.editCashCountBase+'/'+data.id; })
      .fail(showError).always(function(){button.prop('disabled', false);});
  }
  $('#cash-count-create').on('click', function(){createCashCount(false);});
  $('#cash-count-user').on('click', function(){createCashCount(true);});

  function localDateTime(date) {
    var pad = function (value) { return String(value).padStart(2, '0'); };
    return date.getFullYear() + '-' + pad(date.getMonth() + 1) + '-' + pad(date.getDate()) + 'T' + pad(date.getHours()) + ':' + pad(date.getMinutes());
  }
  $('#parking-create').on('click', function () {
    var form = document.getElementById('parking-form');
    form.reset();
    var now = new Date();
    form.elements.hora_ingreso.value = localDateTime(now);
    form.elements.hora_salida.value = localDateTime(now);
    form.elements.id_caja.value = $('[name="caja"]').val() || '';
    $('#parking-form-error').addClass('d-none').text('');
    $('#parking-modal').modal('show');
    window.setTimeout(function () { form.elements.hora_ingreso.focus(); }, 250);
  });
  $('#parking-payment-type').on('change', function () {
    var required = $(this).find(':selected').data('reference') === 1;
    $('#parking-form [name="numero_comprobante"]').prop('required', required);
  });
  $('#parking-form').on('submit', function (event) {
    event.preventDefault();
    var form = $(this), button = $('#parking-save');
    button.prop('disabled', true);
    $('#parking-form-error').addClass('d-none').text('');
    $.ajax({url:cfg.storeParking, type:'POST', data:form.serialize()+'&_token='+encodeURIComponent(cfg.csrf)})
      .done(function (data) {
        $('#parking-modal').modal('hide'); showSuccess(data.message);
        load(cfg.parking, '#cash-parking', '#parking-count');
        load(cfg.cashCounts, '#cash-counts', '#cash-count-total');
        $('a[href="#cash-parking"]').tab('show');
      }).fail(function (xhr) {
        var message = 'No fue posible registrar el cobro de parqueo.';
        if (xhr.responseJSON) {
          message = xhr.responseJSON.message || message;
          if (xhr.responseJSON.errors) {
            var key = Object.keys(xhr.responseJSON.errors)[0];
            if (key) message = xhr.responseJSON.errors[key][0];
          }
        }
        $('#parking-form-error').removeClass('d-none').text(message);
      }).always(function () { button.prop('disabled', false); });
  });
  $(document).on('click', '.parking-delete', function () {
    var id = $(this).data('id');
    if (!window.confirm('¿Eliminar este cobro de parqueo? Esta acción lo retirará del cierre de caja.')) return;
    var button = $(this).prop('disabled', true);
    $.ajax({url:cfg.deleteParkingBase+'/'+id, type:'DELETE', data:{_token:cfg.csrf}})
      .done(function (data) {
        showSuccess(data.message); load(cfg.parking, '#cash-parking', '#parking-count');
        load(cfg.cashCounts, '#cash-counts', '#cash-count-total');
      }).fail(showError).always(function () { button.prop('disabled', false); });
  });
  $('.select2').select2({theme:'bootstrap4', width:'100%'});
  reload();
})(jQuery);
