
@foreach($nombre as $value)
    <tr>
        @if(is_null($value->laboratorio))
            <td>{{$value->nombre}}</td>
            <td colspan="10">No Proviene del Vademecum</td>
        @else
            <td>{{$value->nombre}}</td>
            <td>{{$value->presentacion}}</td>
            <td>{{$value->dosis}}</td>
            <td>{{$value->dosis_pediatrica}}</td>
            <td>{{$value->laboratorio}}</td>
            <td>{{$value->indicaciones}}</td>
            <td>{{$value->contraindicaciones}}</td>
            <td>{{$value->precio_unitario}}</td>
            <td>{{$value->precio_total}}</td>
            <td><a onclick="enviar_a_receta();" class="btn btn-primary" style="color: white;">Enviar a Receta</a></td>
        @endif
    </tr>
@endforeach
