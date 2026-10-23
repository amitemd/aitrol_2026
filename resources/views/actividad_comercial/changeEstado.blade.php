@if($data->estado == 0)
<div class="flex gap-2 mt-4 items-center">
    <div class="h-4 w-4 rounded-full bg-red-500"></div>
    <p class="font-semibold text-sm">Eliminado</p>
</div>
@elseif(is_null($data->estado_transaccion))
<div class="mt-3">
    <label for="estadosTipo{{$data->id}}" class="block mb-2 text-sm font-medium text-gray-900 ">Seleccione un tipo</label>
    <select onchange="viewModal(`{{$data->id}}`, this)" id="estadosTipo{{$data->id}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
        <option selected>Seleccione...</option>
        <option value="2">Reagendado</option>
        <option value="1">Aceptado</option>
        <option value="0">Cancelado</option>
    </select>
</div>
@elseif($data->estado_transaccion == 2)
<div class="flex gap-2 mt-4 items-center">
    <div class="h-4 w-4 rounded-full bg-orange-500"></div>
    <p class="font-semibold text-sm">Reagendado</p>
</div>
@elseif($data->estado_transaccion == 1)
<div class="flex gap-2 mt-4 items-center">
    <div class="h-4 w-4 rounded-full bg-green-500"></div>
    <p class="font-semibold text-sm">Aceptado</p>
</div>
@elseif($data->estado_transaccion == 0)
<div class="flex gap-2 mt-4 items-center">
    <div class="h-4 w-4 rounded-full bg-red-500"></div>
    <p class="font-semibold text-sm">Cancelado</p>
</div>
@endif