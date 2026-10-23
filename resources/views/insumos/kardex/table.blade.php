<style type="text/css">
  .td_der{
    text-align: right;
  }
</style>
<div class="content" id="contenedor">
    <div id="tbl_kardex_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
      <div class="row">
        <div class="table table-responsive">
          <table id="tbl_kardex" class="table-bordered table-hover dataTable1 table-striped" role="grid" aria-describedby="tbl_kardex_info" style="width: 100%;">
            <thead>
              <tr class="well-dark">
                <th colspan="9">{{trans('winsumos.detalle')}}</th>
                <th colspan="3" style="text-align:center">{{trans('winsumos_reportes.entradas')}}</th>
                <th colspan="3" style="text-align:center">{{trans('winsumos_reportes.salidas')}}</th>
                <th colspan="3" style="text-align:center">{{trans('winsumos_reportes.saldos')}}</th>
                <th colspan="2" style="text-align:center">{{trans('winsumos.observacion')}}</th>
              </tr>
              <tr class="well-dark" style="width: 100%;">
                <th width="5%" >{{trans('winsumos.Fecha')}}</th>
                <th width="30%" >{{trans('winsumos_reportes.referencia')}}</th>
                <th width="30%" >{{trans('winsumos.marca')}}</th>
                <th width="30%" >{{trans('winsumos.productos')}}</th>
                <th width="10%" >{{trans('winsumos.serie')}}</th>
                <th width="10%" >{{trans('winsumos.lote')}}</th>
                <th width="10%" >{{trans('winsumos.fecha_vencimiento')}}</th>
                <th width="10%" >{{trans('winsumos.pedidos')}}</th>
                <th width="5%" >{{trans('winsumos_reportes.documento')}}</th>
                <th width="5%" >{{trans('winsumos.cantidad')}}</th>
                <th width="5%" >{{trans('winsumos.precio_unitario')}}</th>
                <th width="5%" >{{trans('winsumos.total')}}</th>
                <th width="5%" >{{trans('winsumos.cantidad')}}</th>
                <th width="5%" >{{trans('winsumos.precio_unitario')}}</th>
                <th width="5%" >{{trans('winsumos.total')}}</th>
                <th width="5%" >{{trans('winsumos.cantidad')}}</th>
                <th width="5%" >{{trans('winsumos.precio_unitario')}}</th>
                <th width="5%" >{{trans('winsumos.total')}}</th>
                <th width="20%" >{{trans('winsumos.detalle')}}</th>
              </tr>
            </thead>
            <tbody>

                @php
                  $cant_ant   = (isset($getAnterior->cantidad))?$getAnterior->cantidad:0;
                  $cant_ant_2   = (isset($getAnterior_2->cantidad))?$getAnterior_2->cantidad:0;
                  $cant_ant -= $cant_ant_2;
                  $iva_ant_2 = 0;
                  $iva_ant = 0;
                  $total_ant = 0;
                  $total_ant_2 = 0;
                  $vu_ant = 0;
                  if(isset($getAnterior->iva)){
                    $iva_ant  = $getAnterior->iva;
                  }
                  if(isset($getAnterior_2->iva)){
                    $iva_ant_2  = $getAnterior_2->iva;
                  }
                  $iva_ant -= $iva_ant_2;
                  if(isset($getAnterior->total)){
                    $total_ant  = $getAnterior->total-$iva_ant;
                  }

                  if(isset($getAnterior_2->total)){
                    $total_ant_2  = $getAnterior_2->total;
                  }
                  $total_ant -= $total_ant_2;
                  if($total_ant > 0 && $cant_ant >0 ){
                    $vu_ant = $total_ant /$cant_ant;
                  }

                @endphp
              <tr>
                <th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th><th ></th>
                <th class="text_tam td_der" width="5%">{{trans('winsumos_reportes.saldo_anterior')}}</th>
                <th class="text_tam td_der" width="5%">@if(isset($getAnterior->cantidad)) {{$cant_ant}} @else 0 @endif</th>
                <th class="text_tam td_der" width="5%">@if(isset($getAnterior->cantidad)) {{number_format($vu_ant,2,'.','') }} @else 0.00 @endif</th>
                <th class="text_tam td_der" width="5%">@if(isset($getAnterior->cantidad)) {{number_format($total_ant,2,'.','') }} @else 0.00 @endif</th>
                <th ></th>
              </tr>
              @php
                $getPrice=0;
                $getPriceant=0;
                $getCount=0;
                $getTotal=0;
                $cantidadant=$cant_ant;
                $anterior= 0;
                if(is_null($anterior)){$anterior=0;}
                $cantidad=$cant_ant;
                $anteriorprecio= 0;
                if(is_null($anteriorprecio)){$anteriorprecio=0;}
                $anteriortotal=0;
                if(is_null($anteriortotal)){$anteriortotal=0;}
                $totalCosto=$total_ant;
                $precioCosto=$vu_ant;
                $contador=0;
                //dd($kardex);
              @endphp
              @foreach ($kardex as $value)
                @php
                  $observ="";
                  if(isset($value->documento_bodega) and $value->documento_bodega->tipo_movimiento->tipo=='I' || $value->documento_bodega->tipo_movimiento->tipo=='R' ){
                    $cantidad+=$value->cantidad;
                  }else{
                    $cantidad= $cantidad-$value->cantidad;
                  }
                  $getPrice+=$value->valor_unitario;
                  $getTotal+=$value->total;
                  $pedido = null;
                @endphp


              <tr class="well">
                <td class="text_tam">{{ date('Y-m-d', strtotime($value->fecha))}}</td>
                <td class="text_tam">@if(isset($value->producto)){{ $value->producto->codigo}} @endif </td>
                <td class="text_tam">@if(isset($value->producto->marca)){{ $value->producto->marca->nombre}}@endif  </td>
                <td class="text_tam">@if(isset($value->producto)){{ $value->producto->nombre}} @endif </td>
                <td class="text_tam">
                  @if (isset($value->documento_origen))
                       - {{$value->documento_origen->serie}}
                       @php
                          $movimiento = \Sis_medico\Movimiento::where('serie',$value->documento_origen->serie)->orderBy('id', 'asc')->first();
                          if (isset($movimiento->pedido)) {
                            $pedido = $movimiento->pedido;
                          }
                        @endphp
                  @endif
                </td>
                <td class="text_tam">
                  @if (isset($value->documento_origen))
                        {{$value->documento_origen->lote}}

                  @endif
                </td>
                <td class="text_tam">
                  @if (isset($value->documento_origen))
                        {{$value->documento_origen->fecha_vence}}
                  @endif
                </td>
                <td class="text_tam"> @if(isset($pedido->id)) {{ $pedido->pedido}} @endif </td>
                <td class="text_tam">{{ $value->descripcion}} {{ $value->bodega->nombre }}
                  @if (isset($value->documento_origen->cabecera))
                    @if (isset($value->documento_origen->cabecera->pedido))
                          || PED: {{$value->documento_origen->cabecera->pedido->pedido}}
                      @if ($value->documento_origen->cabecera->pedido->proveedor)
                          || PRO: {{$value->documento_origen->cabecera->pedido->proveedor->nombrecomercial}}
                      @endif
                    @endif
                  @endif
                 </td>

                @php
                  $tcosto_ed = 0;
                  $tcosto_ed =  $value->valor_unitario*$value->cantidad;

                  //dd($totalCosto);
                  if(isset($value->documento_bodega) and $value->documento_bodega->tipo_movimiento->tipo=='I' || $value->documento_bodega->tipo_movimiento->tipo=='R'){
                    $totalCosto= $totalCosto+$tcosto_ed;
                  }else{
                    $totalCosto= $totalCosto-$tcosto_ed;
                  }
                  if($cantidad != 0){
                    $tunitario = $totalCosto/$cantidad;
                  }else{
                    $tunitario = 0;
                  }

                  $totalCosto =round($tunitario,4)*$cantidad;

                  $tots=$precioCosto*$value->cantidad;
                  $observ= DB::table('ct_ventas')->find($value->id_movimiento);

                @endphp
                @if(isset($value->documento_bodega) and $value->documento_bodega->tipo_movimiento->tipo=='I' || $value->documento_bodega->tipo_movimiento->tipo=='R' )
                  <td class="td_der text_tam">{{ $value->cantidad}}</td>
                  <td class="td_der text_tam">{{ number_format($value->valor_unitario,4,'.','') }}</td>
                  <td class="td_der text_tam">{{ number_format($tcosto_ed,4,'.','') }}</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                @else
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td>&nbsp;</td>
                  <td class="td_der text_tam">{{ $value->cantidad}}</td>
                  <td class="td_der text_tam">{{ number_format($value->valor_unitario,4,'.','') }}</td>
                  <td class="td_der text_tam">{{ number_format($tcosto_ed,4,'.','') }}</td>
                @endif
                <td class="td_der text_tam" @if($cantidad<0) style="color:red;" @php $cantidad = 0; @endphp @endif> {{$cantidad}}</td>
                <td class="td_der text_tam">{{ number_format($tunitario,4,'.','')}}</td>
                <td class="td_der text_tam" @if($totalCosto<0) style="color: red;"  @endif>{{ number_format($totalCosto,4,'.','')}}</td>
                <td class="text_tam"> {{$value->referencia}}
                  @if (isset($value->documento_origen->cabecera))
                    @php
                      $hcp = null; $paciente = null; $recibo_cobro = null; $factura=null;
                      if ($value->documento_origen->cabecera->id_hc_procedimientos) {
                        $hcp = \Sis_medico\hc_procedimientos::find($value->documento_origen->cabecera->id_hc_procedimientos);
                        $hc = null; $agenda = null; $paciente = null;
                        if (isset($hcp->historia)) {
                          $hc = $hcp->historia;
                          $agenda = $hc->agenda;
                          $paciente = $agenda->paciente;
                        }
                        if (isset($agenda->id)) {
                          $recibo_cobro = \Sis_medico\Ct_Orden_Venta::where('id_agenda',$agenda->id)->where('estado',1)->first();
                          if (isset($recibo_cobro->id)) {
                            $factura = \Sis_medico\Ct_ventas::where('orden_venta',$recibo_cobro->id)->first();
                          }
                        }
                      }
                    @endphp
                    @if(!is_null($hcp) and isset($hcp->hc_procedimiento_final->procedimiento)) || PRO: {{$hcp->hc_procedimiento_final->procedimiento->nombre}} @endif
                    @if(!is_null($paciente)) || PAC: {{$paciente->nombre1}} {{$paciente->apellido1}} {{$paciente->apellido2}} @endif
                    @if(!is_null($factura)) || FACT: {{$factura->nro_comprobante}} @endif
                    @if(!is_null($recibo_cobro)) || SEG: {{$recibo_cobro->seguro->nombre}} @endif
                  @endif
                </td>
                {{-- <td class="text_tam">@if($value->documento_bodega->tipo_movimiento->tipo=='I') @if($observ!="") {{$observ->observacion}} @endif @else @if($observ!="") {{$observ->nombres_paciente}} procedimiento: {{$observ->procedimientos}} @endif @endif</td> --}}
              </tr>
              @php
              $observ="";
              //$cantidad=$value->cantidad;
              if(isset($value->documento_bodega) and $value->documento_bodega->tipo_movimiento->tipo=='I' ||  $value->documento_bodega->tipo_movimiento->tipo=='R'){

              $cantidadant+=$value->cantidad;

              }else{

              $cantidadant = $cantidadant-$value->cantidad;
              if ($cantidadant<0) {$cantidadant=0;}

              }
              $getTotalant=0;
              $getPriceant+=$value->valor_unitario;
              $getTotalant+=$value->total;
              @endphp
              @php
              $contador++;
              //dd($getTotal);
              @endphp
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

 
</div>

<script type="text/javascript">
  $(document).ready(function() {

    $('#tbl_kardex').DataTable({
        'paging': true,
        'pageLength': 10,
        "scrollX": true,
        "scrollY": true,
         dom: 'lBrtip',
        'lengthChange': false,
        'searching': true,
        'ordering': true,
        responsive: true,
        'info': false,
        'autoWidth': true,
        buttons: [
        {
          extend: 'excelHtml5',
          footer: true,
          title: 'REPORTE KARDEX ',
          customize: function(doc) {
            var sheet = doc.xl.worksheets['sheet1.xml'];
            //$('row c[r^="D"]', sheet).attr( 's', '64' );
            //console.log($('row c[r^="C"]',sheet))
            $('row', sheet).each( function () {
              //console.log('entra aqui');
              // Get the value
              // console.log($('is t', this))
              var text=  $('is t', this).text();
              if (text.includes('|')) {
                  $('row c',this).attr('s','47');
              }else{
              }
            });
          }
        },
       
      ],
    })

   


  });
</script>
