<div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
  
    <div class="table-responsive">
      <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
        <thead>
          <tr>
            <th>Tipo Pago</th>
            <th>Nro. Transaccion</th>
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
            $total_pago=0; $forma_pago = $orden_venta->pagos;
          @endphp
          @foreach($forma_pago as $value)
            @php
            //dd($value);
              $fi = 0;
              $valor_neto =0;
              if ($value->tipo == '4') {
                /*$fi= 0.07;*/

              }
              if ($value->tipo == '6') {
                /*$fi= 0.02;*/
              }
              $valor_neto= round($value->valor+($value->valor*$fi),2);
              $total_pago += round($valor_neto,2);
            @endphp
          <tr>
            <td>{{$value->metodo->nombre}}</td>
            <td>{{$value->numero}}</td>
            <td>@if($value->tipo_tarjeta != null) {{$value->tarjeta->nombre}} @endif</td>
            <td>@if($value->banco != null) {{$value->ct_banco->nombre}} @endif</td>
            <td>{{$value->valor}}</td>
            <td>{{$fi}}</td>
            <td>{{round($valor_neto,2)}}</td>
            <td><a type="button" onclick="eliminar_forma_gastro('{{$orden_venta->id}}','{{$value->id}}');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-trash"></span></a></td>
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

            <span>{{round($orden_venta->total - $total_pago,2)}}</span>
        </div>
      </div>
    </div>

</div>