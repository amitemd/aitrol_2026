@php 
    $contador='0'
@endphp
@foreach($cuota_hipotecario as $ch)
    <tr id="dato_hip{{$contador}}">
        <td>
            <input type="text" name="valor_cuota_hip{{$contador}}" id="valor_cuota_hip{{$contador}}" style="width: 240px;height:25px" value="{{$ch->valor_cuota}}">
            <input type="hidden" id="visibilidad_hip{{$contador}}" name="visibilidad_hip{{$contador}}" value="1">
        </td>
        <td>
            <input required type="text" name="detalle_cuota_hip{{$contador}}" id="detalle_cuota_hip{{$contador}}" style="width: 240px;height:25px" value="{{$ch->detalle_cuota}}">
        </td>
        <td>
            <button type="button" onclick="eliminar_cuot_hip('{{$contador}}')" class="btn btn-warning btn-margin">Eliminar </button>
        </td>
        @php 
            $contador ++;
        @endphp
    </tr>    
@endforeach
