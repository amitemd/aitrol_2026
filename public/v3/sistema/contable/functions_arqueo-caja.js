(function ($) {
  'use strict';
  var cfg = window.cashCountV3;
  var form = document.getElementById('cash-count-form');
  if (!cfg || !form) return;

  function number(value) {
    var parsed = parseFloat(String(value == null ? '' : value).replace(',', '.'));
    return isFinite(parsed) && parsed >= 0 ? parsed : 0;
  }
  function money(value) { return '$ ' + value.toLocaleString('en-US', {minimumFractionDigits:2, maximumFractionDigits:2}); }
  function summary(markAsChanged) {
    var sales = number($('#ca-cash-sales-input').val());
    var base = number($('#ca-base-input').val());
    var vouchers = number($('#ca-vouchers-input').val());
    var counted = 0;
    $('.ca-detail').each(function () { counted += Math.floor(number(this.value)) * number($(this).data('value')); });
    var expected = sales + base;
    var accounted = counted + vouchers;
    var difference = Math.round((accounted - expected) * 100) / 100;
    var balanced = Math.abs(difference) < 0.01;
    $('#ca-expected-input').val(expected.toFixed(2));
    $('#ca-counted').text(money(counted)); $('#ca-vouchers').text(money(vouchers)); $('#ca-accounted').text(money(accounted)); $('#ca-difference').text(money(difference));
    var status = balanced ? 'Cuadrado' : (difference < 0 ? 'Faltante' : 'Sobrante');
    var klass = balanced ? 'is-balanced' : (difference < 0 ? 'is-short' : 'is-unbalanced');
    $('#ca-status').attr('class', 'ca-status '+klass).text(status);
    $('#ca-difference-card').attr('class', 'ca-summary-card ca-difference '+klass);
    if (markAsChanged !== false) $('#cash-count-pdf').addClass('disabled').attr('aria-disabled', 'true');
    return {balanced:balanced};
  }
  function error(xhr) {
    var message = 'No fue posible guardar el arqueo.';
    if (xhr && xhr.responseJSON) {
      message = xhr.responseJSON.message || message;
      if (xhr.responseJSON.errors) { var key = Object.keys(xhr.responseJSON.errors)[0]; if (key) message = xhr.responseJSON.errors[key][0]; }
    }
    $('#cash-count-success').addClass('d-none'); $('#cash-count-error').removeClass('d-none').text(message);
    Swal.fire({
      title: 'No se pudo completar la acción',
      text: message,
      icon: 'error',
      confirmButtonText: 'Aceptar',
      confirmButtonColor: '#078ca1'
    });
  }
  function applyServerSummary(s) {
    $('#ca-cash-sales-input').val(Number(s.cash_sales).toFixed(2));
    $('#ca-base-input').val(Number(s.base).toFixed(2));
    $('#ca-vouchers-input').val(Number(s.vouchers).toFixed(2));
    $('#ca-counted').text(money(s.counted)); $('#ca-vouchers').text(money(s.vouchers)); $('#ca-accounted').text(money(s.accounted)); $('#ca-difference').text(money(s.difference)); $('#ca-expected-input').val(Number(s.expected).toFixed(2));
    var klass = s.balanced ? 'is-balanced' : (s.difference < 0 ? 'is-short' : 'is-unbalanced');
    $('#ca-status').attr('class','ca-status '+klass).text(s.status); $('#ca-difference-card').attr('class','ca-summary-card ca-difference '+klass);
    $('#cash-count-pdf').toggleClass('disabled', !s.balanced).attr('aria-disabled', s.balanced ? 'false' : 'true');
  }
  function applyMovementSummary(s) {
    // El movimiento ya quedó guardado, pero las denominaciones pueden contener
    // cambios todavía no enviados. Solo actualizar los vales y recalcular con
    // las cantidades que el usuario conserva visibles en el formulario.
    $('#ca-cash-sales-input').val(Number(s.cash_sales).toFixed(2));
    $('#ca-vouchers-input').val(Number(s.vouchers).toFixed(2));
    summary(true);
  }
  $(form).on('input', '.ca-recalculate,.ca-detail', function () { summary(true); });
  // Recalcular al abrir evita mezclar valores restaurados por el navegador
  // con un total renderizado anteriormente por el servidor.
  summary(false);
  $('#cash-count-pdf').on('click', function (event) { if ($(this).hasClass('disabled')) event.preventDefault(); });
  $(form).on('submit', function (event) {
    event.preventDefault();
    $('.ca-detail').each(function () { this.value = Math.max(0, Math.floor(number(this.value))); });
    var button = $('#cash-count-save').prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Guardando…');
    $.ajax({url:cfg.save,type:'POST',data:$(form).serialize()}).done(function (data) {
      $('#cash-count-error').addClass('d-none'); $('#cash-count-success').removeClass('d-none').text(data.message);
      applyServerSummary(data.summary);
      Swal.fire({
        title: 'Arqueo guardado',
        text: data.message,
        icon: 'success',
        confirmButtonText: 'Aceptar',
        confirmButtonColor: '#078ca1'
      });
    }).fail(error).always(function () { button.prop('disabled', false).html('<i class="fas fa-save mr-1"></i> Guardar arqueo'); });
  });
  $('#ca-movement-add').on('click', function () {
    var button=$(this), data={
      _token:$(form).find('[name="_token"]').val(), tipo:$('#ca-movement-type').val(),
      fecha:$('#ca-movement-date').val(), beneficiario:$('#ca-movement-beneficiary').val(),
      concepto:$('#ca-movement-concept').val(), numero_comprobante:$('#ca-movement-reference').val(),
      valor:$('#ca-movement-value').val()
    };
    button.prop('disabled',true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Agregando…');
    $.ajax({url:cfg.movementStore,type:'POST',data:data}).done(function(response){
      $('#ca-movement-rows').html(response.html); applyMovementSummary(response.summary);
      $('#ca-movement-beneficiary,#ca-movement-concept,#ca-movement-reference,#ca-movement-value').val('');
      $('#cash-count-error').addClass('d-none'); $('#cash-count-success').removeClass('d-none').text(response.message);
      $('#ca-movement-concept').focus();
    }).fail(error).always(function(){button.prop('disabled',false).html('<i class="fas fa-plus mr-1"></i> Agregar');});
  });
  $(document).on('click','.ca-movement-delete',function(){
    var button=$(this);
    Swal.fire({
      title:'¿Eliminar movimiento?',
      text:'El movimiento dejará de formar parte del arqueo y el cuadre se calculará nuevamente.',
      icon:'warning',
      showCancelButton:true,
      confirmButtonColor:'#dc3545',
      cancelButtonColor:'#6c757d',
      confirmButtonText:'Sí, eliminar',
      cancelButtonText:'Cancelar',
      reverseButtons:true
    }).then(function(result){
      if(!result.isConfirmed)return;
      button.prop('disabled',true);
      $.ajax({url:button.data('url'),type:'POST',data:{_token:$(form).find('[name="_token"]').val(),_method:'DELETE'}}).done(function(response){
        $('#ca-movement-rows').html(response.html); applyMovementSummary(response.summary);
        $('#cash-count-error').addClass('d-none'); $('#cash-count-success').removeClass('d-none').text(response.message);
        Swal.fire({title:'Movimiento eliminado',text:response.message,icon:'success',timer:1800,showConfirmButton:false});
      }).fail(error).always(function(){button.prop('disabled',false);});
    });
  });
})(jQuery);
