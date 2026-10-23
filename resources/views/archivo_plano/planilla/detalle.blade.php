<style type="text/css">

  td{
    text-align: center;
    font-size: 13px;
  }

</style>

<div class="table-responsive col-md-12">
  <table  cellspacing="0" cellpadding="3" rules="all" id="grdgitem" style="background-color:White;border-color:#CCCCCC;border-width:1px;border-style:None;font-family:Arial;font-size:10px;width:100%;border-collapse:collapse;">
    <thead>
    <tr style="color:White;background-color:#006699;font-weight:bold;">
        <th style="width: 5%;padding: 1px;color: white;">ID</th>
        <th style="width: 10%;padding: 1px;color: white;">FECHA</th>
        @if($idseguro == 5)
        <th style="width: 10%;padding: 1px;color: white;">CLASIFICADOR</th>
        @endif
        <th style="width: 5%;padding: 1px;color: white;">TIPO</th>
        <th style="width: 10%;padding: 1px;color: white;">CÒDIGO</th>     
        <th style="width: 30%;padding: 1px;color: white;">DESCRIPCIÒN</th>
        <th style="width: 5%;padding: 1px;color: white;">CANTIDAD</th>
        <th style="width: 5%;padding: 1px;color: white;">VALOR U</th>
        <th style="width: 5%;padding: 1px;color: white;">SUBTOTAL</th>
        <th style="width: 5%;padding: 1px;color: white;">10%</th>
        <th style="width: 5%;padding: 1px;color: white;">IVA</th>
        <th style="width: 5%;padding: 1px;color: white;">TOTAL</th>               
        <th style="width: 10%;padding: 1px;color: white;">ACCIÒN</th>
      </tr>
    </thead>
    <tbody>
      @php 
        $i=0;
        $existe = false; 
      @endphp
      @foreach ($detalles as $value)
        @php
          $plano_cabecera = Sis_medico\Archivo_Plano_Cabecera::where('id',$value->id_ap_cabecera)->first();
          $fecha=substr($value->fecha,0,10);
          $fecha_inv=date("d-m-Y",strtotime($fecha));
          $ap_tipo_examen = Sis_medico\Ap_Tipo_Examen::where('tipo',$value->tipo)->first();
          $producto = null;
          if($value->id_producto != null){
            $producto = Sis_medico\Ct_productos::find($value->id_producto);

          }
          $ap_procedimiento = Sis_medico\Ap_Procedimiento::where('tipo', $value->tipo)->where('codigo', $value->codigo)->first();

          $nuevo_iva = $value->valor_unitario * $value->porcentaje_iva;
          $p10_unitario = $value->valor_unitario * $value->porcent_10;
          $total_item = ($value->cantidad * $value->valor) + ($value->cantidad * $nuevo_iva);
          if(!is_null($nuevo_iva)){
            $total_item = ($value->cantidad * $value->valor_unitario) + ($value->cantidad * $value->iva_unitario) + ($value->cantidad * $value->p10_unitario);
          }
          $total_item = round($total_item,4);
        @endphp
        <tr role="row" style="padding: 1px;"id="detalle{{ $value->id }}">@php $i++; @endphp
          <td style="font-size: 11px; padding: 1px;" >{{$i}} </td>
          <td style="font-size: 11px; padding: 1px;">{{$fecha_inv}}</td>
          @if($plano_cabecera->id_seguro==5)
          <td style="font-size: 11px; padding: 1px;">{{$value->clasificador}}</td>
          @endif
          <td style="font-size: 11px; padding: 1px;">{{$value->tipo}}</td>
          <td style="font-size: 11px; padding: 1px;"> 
              <b> codigo: @if($value->codigo == 16) 911111 @else {{$value->codigo}} @endif </b> @if($ap_tipo_examen->tipo_ex == 'IMM' ) {{$value->serie}} @if(!is_null($producto))<br> {{ $producto->codigo }} @endif @else  @if($ap_tipo_examen->tipo_ex == 'FAR' ) {{$value->codigo_medicina}} @else {{$value->codigo}} @endif @endif @if($value->tipo == 'IDE') {{$ap_procedimiento->codigo_insumos}} @endif</td>
          <td style="font-size: 11px; padding: 1px;">{{$value->descripcion}}</td>
          <td style="font-size: 11px; padding: 1px;">{{$value->cantidad}}</td>
          <td style="font-size: 11px; padding: 1px;">@if($ap_tipo_examen->tipo_ex == 'IMM') ${{ number_format($value->valor_unitario, 4, ',', ' ') }} @else ${{ number_format($value->valor, 4, ',', ' ') }} @endif</td>
          <td style="font-size: 11px; padding: 1px;">${{ number_format($value->subtotal, 4, ',', ' ') }}</td>
          <td style="font-size: 11px; padding: 1px;">${{ number_format($value->porcentaje10, 4, ',', ' ') }}</td>
          <td style="font-size: 11px; padding: 1px;">@if($ap_tipo_examen->tipo_ex == 'IMM') ${{ number_format(round($value->subtotal * $value->porcentaje_iva, 4), 4, ',', ' ') }} @else ${{ number_format($value->iva, 4, ',', ' ') }} @endif</td>
          <td style="font-size: 11px; padding: 1px;">@if($ap_tipo_examen->tipo_ex == 'IMM') ${{ number_format($total_item, 4, ',', ' ') }} @else ${{ number_format($value->total, 4, ',', ' ') }} @endif</td>
          <td style="font-size: 11px;">
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
    Swal.fire({
        title: "¿Desea Eliminar el Item?",
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonText: "Continuar",
        denyButtonText: "Cancelar",
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        $.ajax({
            url:"{{ url('archivo_plano/planilla/detalle/procedimiento/eliminar') }}/" + i,
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            type: 'POST',
            data: { id: i },
            success: function(data)
            {
              if( data.estado == 'ok'){
                $('#detalle'+i).hide();
              }else{
                swal("Error!",data.msn,"error");       
              }
                
            }
        });          
                
      } else if (result.isDenied) {
          //Swal.fire("Changes are not saved", "", "info");
      }
    });
    
  }

</script>