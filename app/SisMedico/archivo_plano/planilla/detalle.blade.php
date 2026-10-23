<div class="table-responsive col-md-12">
  <table  cellspacing="0" cellpadding="3" rules="all" id="grdgitem" style="background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;font-family:Arial;font-size:10px;width:100%;border-collapse:collapse;">
    <thead>
      <tr style="color:White;background-color:#006699;font-weight:bold;">
        <th width="10" style="padding: 1px;color: white;">ID</th>
        <th width="10" style="padding: 1px;color: white;">FECHA</th>
        @if($idseguro == 5)
        <th width="10" style="padding: 1px;color: white;">CLASIFICADOR</th>
        @endif
        <th width="10" style="padding: 1px;color: white;">TIPO</th>
        <th width="10" style="padding: 1px;color: white;">CÒDIGO</th>     
        <th width="10" style="padding: 1px;color: white;">DESCRIPCIÒN</th>
        <th width="10" style="padding: 1px;color: white;">CANTIDAD</th>
        <th width="10" style="padding: 1px;color: white;">VALOR</th>
        <th width="10" style="padding: 1px;color: white;">IVA</th>
        <th width="10" style="padding: 1px;color: white;">TOTAL</th>               
        <th width="5" style="padding: 1px;color: white;">ACCIÒN</th>
      </tr>
    </thead>
    <tbody>
      @php $i=0; @endphp
      @foreach ($detalles as $value)
        @php
          $plano_cabecera = Sis_medico\Archivo_Plano_Cabecera::where('id',$value->id_ap_cabecera)->first();
          $fecha=substr($value->fecha,0,10);
          $fecha_inv=date("d-m-Y",strtotime($fecha));
        @endphp
        <tr role="row" style="padding: 1px;">@php $i++; @endphp
          <td style="font-size: 11px; padding: 1px;" >{{$i}}</td>
          <td style="font-size: 11px; padding: 1px;">{{$fecha_inv}}</td>
          @if($plano_cabecera->id_seguro==5)
          <td style="font-size: 11px; padding: 1px;">{{$value->clasificador}}</td>
          @endif    
          <td style="font-size: 11px; padding: 1px;">{{$value->tipo}}</td>
          <td style="font-size: 11px; padding: 1px;">@if($value->tipo == 'IV' || $value->tipo == 'I' || $value->tipo == 'M') &nbsp; @else {{$value->codigo}} @endif</td>
          <td style="font-size: 11px; padding: 1px;">{{$value->descripcion}}</td>
          <td style="font-size: 11px; padding: 1px;">{{$value->cantidad}}</td>
          <td style="font-size: 11px;">{{$value->valor}}</td>
          <td style="font-size: 11px; padding: 1px;">{{$value->porcentaje_iva}}</td>
          <td style="font-size: 11px;">{{round($value->total,2)}}</td>
          <td style="font-size: 11px;">
            <!--<a  href="" data-toggle="modal" data-target="#mdetalle{{ $value->id }}"  class="btn btn-warning btn-xs">
              <i class="fa fa-edit"></i>
            </a>-->
            <a href="" data-toggle="modal" data-target="#upd_item_iess" data-remote="{{route('update_item_modal.iess', ['id' => $value->id,'indice' => $i])}}" class="btn btn-warning btn-xs">
              <i class="fa fa-edit"></i>
            </a>
            <a onclick="elimino('{{ $value->id }}')" class="btn btn-danger btn-xs">
            <i class="fa fa-trash"></i>
            </a>
          </td>     
        </tr>
      
        <!--<div class="modal fade" id="mdetalle{{ $value->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
          <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            <iframe id="dito" class="embed-responsive-item" src="http://192.168.75.125/sis_medico_prb/public/archivo_plano/planilla/editar/{{ $value->id }}" allowfullscreen style="width:100%; height:500px"></iframe>
            </div>
          </div>
        </div>-->

        <!--Ventana Modal Item Iess -->
        <div class="modal fade" id="upd_item_iess" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content" style="width: 95%;">
                </div>
            </div>  
        </div>

    @endforeach
    </tbody>
  </table>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<script>

  $('#upd_item_iess').on('hidden.bs.modal', function(){
    $(this).removeData('bs.modal');
  });
  
  function elimino(i) {
      $.ajax({
          url:"{{ url('archivo_plano/planilla/detalle/procedimiento/eliminar') }}/" + i,
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          type: 'POST',
          data: { id: i },
          success: function(response)
          {
            //alert('Procedimiento eliminado');
            swal({
                    title: "Item Eliminado",
                    icon: "success",
                    type: 'success',
                    buttons: true,
            })
            window.location.reload(true);
          }
      });
    }

</script>