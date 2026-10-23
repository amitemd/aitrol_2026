@if($titleTrueOrFalse == 0)
<tr class="bg-white border-b hover:bg-gray-50 text-gray-50 text-center">
    <th scope="col" colspan="3" class="px-6 py-3 w-[400px]  sm:rounded-lg">
        .
    </th>
    <th class="bg-[#45b5b2] px-6 py-3 sm:rounded-tl-lg" scope="col" colspan="2">
        Si
    </th>
    <th class="bg-[#45b5b2] px-6 py-3 sm:rounded-tr-lg" scopse="col" colspan="2">
        No
    </th>
</tr>

@endif

<tr class="bg-white border-b hover:bg-gray-50 ">
    <th scope="row" colspan="3" class="px-6 py-4 font-medium text-gray-900 whitespace-normal max-w-[550px] ">
        <input type="hidden" class="pregunta" value="{{$item['id']}}">
        {{$item["pregunta"]}}
    </th>
    @foreach($item["valores"] as $sel)
    @if($sel["nombre"] == "Si")
    <td colspan="2" class="px-6 py-4 text-center">
        <input type="radio" id="super-happy{{$item['id']}}" name="{{$item['id']}}" class="super-happy check_{{$item['id']}}" value="{{$sel['valor']}}" />
        <label class="super-happy" for="super-happy{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.check")
        </label>
    </td>
    @endif

    @if($sel["nombre"] == "No")
    <td colspan="2" class="px-6 py-4 text-center">
        <input type="radio" class="super-sad check_{{$item['id']}}" name="{{$item['id']}}" id="super-sad{{$item['id']}}" value="{{$sel['valor']}}" />
        <label class="super-sad" for="super-sad{{$item['id']}}">
            @include("laboratorio.nueva_encuesta_labs.icons.x")
        </label>
    </td>
    @endif

    @endforeach
</tr>