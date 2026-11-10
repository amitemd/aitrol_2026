(function ($) {
  'use strict';
  var cfg = window.labCashClosingV3;
  if (!cfg || !document.getElementById('lab-cash-filters')) return;
  function params() { return $('#lab-cash-filters').serialize(); }
  function load() {
    $('#lab-cash-results').html('<div class="text-center text-muted p-4"><i class="fas fa-spinner fa-spin mr-2"></i>Cargando…</div>');
    $.getJSON(cfg.data + '?' + params()).done(function (response) {
      $('#lab-cash-results').html(response.html); $('#lab-cash-count').text(response.count);
      $.each(response.totals, function (key, value) { $('[data-total="'+key+'"]').text('$'+Number(value).toFixed(2)); });
      $('#lab-cash-error').addClass('d-none');
    }).fail(function () { $('#lab-cash-error').removeClass('d-none').text('No fue posible consultar el cierre de laboratorio.'); });
  }
  $('#lab-cash-filters').on('submit', function (event) { event.preventDefault(); load(); });
  $('#lab-cash-clear').on('click', function () { this.form.reset(); $('#lab-cash-user').val(null).trigger('change'); load(); });
  $('#lab-cash-user').select2({theme:'bootstrap4',width:'100%',allowClear:true,placeholder:'Todos',ajax:{url:cfg.users,dataType:'json',delay:250,data:function(p){return {q:p.term};},processResults:function(d){return d;}}});
  $('.select2').select2({theme:'bootstrap4',width:'100%'});
  $('[data-lab-report]').on('click', function () {
    var form = $('#lab-cash-report-form').attr('action', $(this).data('lab-report')).empty();
    form.append($('<input>',{type:'hidden',name:'_token',value:cfg.csrf}));
    $('#lab-cash-filters').serializeArray().forEach(function(field){form.append($('<input>',{type:'hidden',name:field.name,value:field.value}));});
    form.append($('<input>',{type:'hidden',name:'fecha',value:$('[name="fecha_desde"]').val()}));
    form.trigger('submit');
  });
  load();
})(jQuery);
