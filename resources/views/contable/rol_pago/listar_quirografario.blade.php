@php 
    $contador='0'
@endphp
@foreach($cuota_quirografario as $cq)
    <tr id="dato_quiro{{$contador}}">
        <td>
            <input type="text" name="valor_cuota_quir{{$contador}}" id="valor_cuota_quir{{$contador}}"style="width: 240px;height:25px" value="{{$cq->valor_cuota}}">
            <input type="hidden" id="visibilidad_quiro{{$contador}}" name="visibilidad_quiro{{$contador}}" value="1">
        </td>
        <td>
            <input required type="text" name="detalle_cuota_quir{{$contador}}" id="detalle_cuota_quir{{$contador}}" style="width: 240px;height:25px" value="{{$cq->detalle_cuota}}">
        </td>
        <td>
            <button type="button" onclick="eliminar_cuot_quiro('{{$contador}}')" class="btn btn-warning btn-margin">Eliminar </button>
        </td>
        @php 
            $contador ++;
        @endphp
    </tr>    
@endforeach

