<tr class="bg-white border-b hover:bg-gray-50 ">
    <th scope="row" colspan="2" class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px] ">
        <input type="hidden" class="pregunta" value="{{$item['id']}}">
        {{$item["pregunta"]}}
    </th>
    @foreach($item["valores"] as $valor)
    @if($valor["nombre"] == "Muy Bueno")
    <td class="px-4 py-2 sm:px-6 sm:py-4">
        <input type="radio" id="super-happy{{$item['id']}}" name="{{$item['id']}}" class="check_{{$item['id']}} checkEmoji" value="{{$valor['valor']}}" />
        <label class="radio-label super-happy" for="super-happy{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.muy_bueno")
        </label>
    </td>
    @endif
    @endforeach


    @foreach($item["valores"] as $valor)
    @if($valor["nombre"] == "Bueno")
    <td class="px-4 py-2 sm:px-6 sm:py-4">
        <input type="radio" id="happy{{$item['id']}}" name="{{$item['id']}}" class="happy check_{{$item['id']}}" value="{{$valor['valor']}}" />
        <label class="radio-label happy" for="happy{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.bueno")
        </label>
    </td>
    @endif
    @endforeach


    @foreach($item["valores"] as $valor)
    @if($valor["nombre"] == "Ni bueno ni malo")
    <td class="px-4 py-2 sm:px-6 sm:py-4">
        <input type="radio" class="neutral check_{{$item['id']}}" name="{{$item['id']}}" id="neutral{{$item['id']}}" value="{{$valor['valor']}}" />
        <label class="radio-label neutral" for="neutral{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.neutro")
        </label>
    </td>
    @endif
    @endforeach


    @foreach($item["valores"] as $valor)
    @if($valor["nombre"] == "Malo")
    <td class="px-4 py-2 sm:px-6 sm:py-4">
        <input type="radio" class="neutral-2 check_{{$item['id']}}" name="{{$item['id']}}" id="neutral-2{{$item['id']}}" value="{{$valor['valor']}}" />
        <label class="neutral-2 radio-label" for="neutral-2{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.malo")
        </label>
    </td>
    @endif
    @endforeach


    @foreach($item["valores"] as $valor)
    @if($valor["nombre"] == "Muy Malo")
    <td class="px-4 py-2 sm:px-6 sm:py-4">
        <input type="radio" class="super-sad check_{{$item['id']}}" name="{{$item['id']}}" id="super-sad{{$item['id']}}" value="{{$valor['valor']}}" />
        <label class="radio-label super-sad" for="super-sad{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.muy_malo")
        </label>
    </td>
    @endif
    @endforeach
</tr>