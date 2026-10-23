@php 
    $contador='0'
@endphp
@foreach($venta_pago as $xv)
    <tr id="dato_pago{{$contador}}">
        <td>
            <select required name="id_tip_pago{{$contador}}" id="id_tip_pago{{$contador}}" style="width: 175px;height:25px" onchange="revisar_componentes(this,'{{$contador}}');">
                <option value="">Seleccione</option>
                @foreach($tipo_pago as $value)
                    <option @if($xv->tipo==$value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
            </select>
            <input required type="hidden" id="visibilidad_pago{{$contador}}" name="visibilidad_pago{{$contador}}" value="1">
        </td>
        <td>
            <input required type="date" class="input-number" value="{{$xv->fecha}}" name="fecha{{$contador}}" id="fecha{{$contador}}" style="width: 110px;">
        </td>
        <td>
            <select required id="tipo_tarjeta{{$contador}}" name="tipo_tarjeta{{$contador}}" style="width: 175px;height:25px">
                <option value="">Seleccione...</option> 
                @foreach($tipo_tarjeta as $tipo_t) 
                    <option @if($xv->tipo_tarjeta==$tipo_t->id) selected @endif value="{{$tipo_t->id}}">{{$tipo_t->nombre}}
                @endforeach
            </select>
        </td>
        <td>
            <input required type="text" name="numero{{$contador}}" id="numero{{$contador}}" style="width: 100px;" required value="{{$xv->numero}}">
        </td>
        <td>
            <select required name="id_banco{{$contador}}" id="id_banco{{$contador}}" style="width: 175px;height:25px">
                <option value="">Seleccione...</option>
                @foreach($lista_banco as $value)
                    <option @if($xv->banco==$value->id) selected @endif value="{{$value->id}}">{{$value->nombre}}</option>
                @endforeach
            </select>
        </td>
        <td>
            <input required style="text-align:center;" type="checkbox" name="fi{{$contador}}" id="fi{{$contador}}" onchange="revision_total('{{$contador}}')" value="0" @if($xv->posee_fi=='1') checked @endif>
        </td>
        <td>@php if($xv->posee_fi=='1'){ $xbase = round(($xv->valor / (1 + $xv->p_fi)),2); } else{ $xbase = $xv->valor;} @endphp
            <input required type="text" id="valor_base{{$contador}}" name="valor_base{{$contador}}" style="width: 100px;"  value="{{$xbase}}" onchange="revision_total('{{$contador}}')" onkeypress="return soloNumeros(event);">
        </td>
        <td>
            <input required type="text" id="total{{$contador}}" name="total{{$contador}}" style="width: 100px;" onkeypress="return soloNumeros(event);" onchange="return redondea_valor_base(this,'{{$contador}}',2);">
        </td>
        <td>
            <button type="button" onclick="eliminar_form_pag('{{$contador}}')" class="btn btn-warning btn-margin">Eliminar </button>
        </td>
        @php 
            $contador ++;
        @endphp
    </tr>    
@endforeach

<script type="text/javascript">

    var i;
    for (i = 0; i < {{$contador}}; i++) {
        element = document.getElementById('id_tip_pago'+i);
        revisar_componentes(element, i);
    }

</script>