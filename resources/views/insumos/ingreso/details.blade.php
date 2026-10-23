
@if($productos!='[]')
    @foreach($productos as $x)
    @php
        $comprobar = null;
        $precio_neto= $x->cantidad * $x->precio;
        $pedidos= DB::table('pedido')->where('pedido',$x->pedido)->first();
        if($pedidos!=null){
            $comprobar = DB::table('detalle_pedido')->where('serie',$x->serie)
                                                    ->first();
            $cantidad = $x->cantidad;
            if(!is_null($comprobar)){
                $cantidad= $cantidad-$comprobar->cantidad;
            }
            if ($cantidad<0) {
                //$cantidad = 0;
                $cantidad = $x->cantidad;
            } 
            if (isset($anterior)and$anterior==1) {
            }
        }
        # se uso en algun procedimiento 
        $nombodega = "";
        $mov_paciente = null;
        $movimiento = DB::table('movimiento')->where('serie',$x->serie)->first();
        $bodega = \Sis_medico\Bodega::find(env('BODEGA_PRINCIPAL',1));
        if (!is_null($movimiento)) {
            $mov_paciente = DB::table('movimiento_paciente')->where('id_movimiento',$movimiento->id)->first();
            $nombodega = "BODEGA PENTAX";
        } else {
            if($x->bodega!="" and $x->bodega!=null){
                $nombodega = $x->bodega;
            } elseif (isset($bodega->id)) {
                $nombodega = trim($bodega->nombre);
            }
        }
        
    @endphp
    @if ($cantidad!=0 or isset($conglomerada)) 
    <tr bgcolor="#FF0000">
        <td> <input type="text" style="width: 99%;height:20px;" class="form-control input-sm pedido" readonly
                name="pedido[]" value="{{$x->pedido}}@if(isset($comprobar->id_pedido))/{{$comprobar->id_pedido}}@endif">
        </td>
        <td> <input type="text" style="width: 99%;height:20px;" class="form-control input-sm" readonly name="proveedor[]"
                value="@if ($x->nombrecomercial!="" and $x->nombrecomercial!=null) {{$x->nombrecomercial}} @else {{$x->razonsocial}} @endif">
            <input type="hidden" value="{{$x->id_proveedor}}">
        </td>
        <td> <input type="text" style="width: 99%;height:20px;" class="form-control input-sm" readonly name="codigo[]"
                value="{{$x->codigo}}">
        </td>
        <td> <input type="text" style="width: 99%;height:20px;" class="form-control input-sm" readonly name="nombre[]"
                value="{{$x->nombre}}">
            <input type="hidden" name="id[]" value="{{$x->id_producto}}">
        </td>
        <td> <input type="text" style="width: 97%;height:20px; text-align:right;" class="form-control input-sm cneto"
               @if(isset($anterior)and$anterior==1) onchange="cantidad_permitida({{$x->cantidad}},this)" @endif name="cantidad[]" required value="{{$cantidad}}"> </td>
        <td> 
            @php $bodega = \Sis_medico\Bodega::find(env('BODEGA_PRINCIPAL',1)); @endphp
            <input type="text" style="width: 99%;height:20px;" class="form-control input-sm" readonly name="nbodega[]"
            value="{{$nombodega}}">
            <input type="hidden" name="bodega[]" value="@if(!is_null($x->id_bodega) and $x->id_bodega !="" ){{$x->id_bodega}}@else{{env('BODEGA_PRINCIPAL',1)}}@endif">
        </td>
        <td> <input type="text" style="width: 100%;height:20px;" class="form-control input-sm serie" readonly name="serie[]"
                value="{{$x->serie}}" readonly> </td> 
        <td> <input type="text" style="width: 99%;height:20px;" class="form-control input-sm" name="lote[]" readonly
                value="{{$x->lote}}"> </td>
        <td> <input type="date" style="width: 99%;height:20px;" name="fecha_vencimiento[]" class="form-control input-sm"
                readonly value="{{$x->fecha_vencimiento}}"> </td>
        <td> <input type="text" style="width: 95%;height:20px; text-align:right;" class="form-control input-sm pneto preciouni"
            onkeypress="return isNumberKey(event)" name="precio[]" onchange="recalcularValores()" value="{{number_format($x->precio,2, '.', '')}}">
        </td>
        <td>
            <input class="form-control input-sm text-right pdesc" type="text" readonly
                style="width: 95%;height:20px; text-align:right;" name="pDescuento[]" onkeypress="return isNumberKey(event)"
                onblur="this.value=parseFloat(this.value).toFixed(0);" @if($x->descuentop!=null) value="{{$x->descuentop}}"
            @else value="0.00" @endif required>
            <input type="hidden" readonly onkeypress="return isNumberKey(event)"
                onblur="this.value=parseFloat(this.value).toFixed(0);" value="0" required>
        </td> 
        <td>
            <input class="form-control input-sm pneto totalUnitario" onchange="recalcularValores()" readonly type="text"
                style="width: 95%;height:20px; text-align:right;" onkeypress="return isNumberKey(event)"
                onblur="this.value=parseFloat(this.value).toFixed(2);" value="{{number_format($precio_neto,2, '.', '')}}" required>
        </td>
        <td align="right">
            <input class="form" type="checkbox" readonly name="valor_iva[]" @if($x->iva==1) checked="checked" @endif
            value="1">
        </td>
        <td>
            <button type="button" class="btn btn-danger  delete">
                <i class="glyphicon glyphicon-trash" aria-hidden="true"></i>
            </button>
        </td>
    </tr>
    @endif


    @endforeach
@else
<script>
    Swal.fire("Mensaje : ","No se encontraron resultados","error");
</script>
@endif
{{-- @else
    <script>
        Swal.fire("Mensaje : ","El item ya fue facturado","error");
    </script>
@endif --}}