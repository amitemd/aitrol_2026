@php
  $total_pago=0; $forma_pago = $orden_venta->pagos;
@endphp
<div id="div_calcula_oda">
  <div class="row">
    @php
    $total_det = $orden_venta_detalle->cantidad * $orden_venta_detalle->precio;
    @endphp
    <div class="col-md-4">
      <div class="col-md-8">
        <label>% @lang('laboratorio.cobrarPaciente')</label>
      </div>
      <div class="col-md-4">
        <input type="hidden" name="id_orden_venta" value="{{$orden_venta->id}}">
        <input type="hidden" id="total_orden_venta" value="{{$total_det}}">
        <input type="text" id="oda_gc" name="oda_gc" class="form-control input-sm" onchange="valor_oda();guardar_oda();" value="{{$orden_venta_detalle->p_oda}}">
      </div>
    </div>
    <div class="col-md-8">
      <div class="col-md-6">
        <div class="col-md-7">
          <label> @lang('laboratorio.TotalPagar'):</label>
        </div>
        <div class="col-md-5">
          <input type="text" name="total_ov" class="form-control input-sm" value="{{$orden_venta->total}}" readonly>
        </div>
      </div>
      <div class="col-md-6">
        <div class="col-md-7">
          <label>@lang('laboratorio.ValorOda'):</label>
        </div>
        <div class="col-md-5">
          <input type="text" name="total_oda_ov" class="form-control input-sm" value="{{$orden_venta->valor_oda}}" readonly>
        </div>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
  $(document).ready(function() {
    @if($forma_pago -> count() > 0)
    $('#oda_gc').prop('readonly', true);
    @endif
  });
</script>