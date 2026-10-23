<div class="col-md-6">
  <label>Total A Pagar:</label>
  <span><b>$ @if($orden->cobrar_pac_pct < 100) {{ $orden->total_con_oda }}  @else {{ $orden->total_valor }} @endif</b></span> 
  @if($orden->cobrar_pac_pct < 100)
    <label>Oda:</label>@php $dif = $orden->total_valor - $orden->total_con_oda; @endphp
    <span><b>$ {{ $dif }}</b></span>   
  @endif
</div>
<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  <div class="row">
    <div class="table-responsive col-md-12">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
        <thead>
          <tr>
            <th>Tipo Pago</th>
            <th>Numero Transaccion</th>
            <th>Tarjeta</th>
            <th>Banco</th>
            <th>Valor</th>
            <th>Fi Administrativo</th>
            <th>Valor Neto</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          @php
            $total_pago=0;
          @endphp
          @foreach($forma_pago as $value)
            @php
              $fi = 0;
              $valor_neto =0;
              if ($value->id_tipo_pago == '4') {
                /*$fi= 0.07;*/

              }
              if ($value->id_tipo_pago == '6') {
                /*$fi= 0.045;*/
              }
              $valor_neto= $value->valor+($value->valor*$fi);
              $total_pago += round($valor_neto,2);
            @endphp
            <tr>
              <td>{{$value->tipo_pago->nombre}}</td>
              <td>{{$value->numero}}</td>
              <td>@if($value->tipo_tarjeta != null) {{$value->tarjetas->nombre}} @endif</td>
              <td>@if($value->banco != null) {{$value->bancos->nombre}} @endif</td>
              <td>{{$value->valor}}</td>
              <td>{{$fi}}</td>
              <td>{{round($valor_neto,2)}}</td>
              <td><a type="button" onclick="eliminar('{{$id_orden}}','{{$value->id}}')" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="row">
        <div class="col-md-4">
          <label>Total Pagado</label>
          <span>{{round($total_pago,2)}}</span>
        </div>
        <div class="col-md-4">
          <label>Pendiente</label>  
          <span>@if($orden->cobrar_pac_pct < 100) {{ round($orden->total_con_oda - $total_pago,2) }} @else {{ round($orden->total_valor - $total_pago,2) }} @endif</span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-md-12" id="crear_registro"></div>
</div>
