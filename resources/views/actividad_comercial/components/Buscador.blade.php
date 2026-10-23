{{--@if($rolUsuario == 17)
<form method="POST" id="reporte_master" action="{{ route('actividad_comercial.buscador_prospecto') }}">
{{ csrf_field() }}
<div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
    <div class="mb-5">
        <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
        <input type="date" name="fecha_desde" id="fecha_desde" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{$fecha_desde}}">
    </div>
    <div class="mb-5">
        <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
        <input type="date" name="fecha_hasta" id="fecha_hasta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" value="{{$fecha_hasta}}">
    </div>
</div>
<div class="flex justify-end">
    <button type="submit" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
        <i class="fa fa-search" aria-hidden="true"></i>
        Buscar
    </button>
</div>
</form>
@endif --}}
<form method="GET" id="reporte_master" action="{{ route('actividad_comercial.index') }}">
    <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
        <div class="mb-5">
            <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
            <input type="date" name="fecha_desde" id="fecha_desde" value="{{isset($params['fecha_desde']) ? $params['fecha_desde'] : null }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>
        <div class="mb-5">
            <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
            <input type="date" name="fecha_hasta" id="fecha_hasta" value="{{isset($params['fecha_hasta']) ? $params['fecha_hasta'] : null }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
        </div>


        <div class="mb-2">
            <label class="texto" for="nombre_cliente">Cliente: </label>
            <select name="id_cliente" id="id_cliente" class="form-control select2_nombres_clientes" style="width: 100%;">
                <option value="">Seleccione...</option>
            </select>
        </div>

        @if($rolUsuario != 17)
        <div class="mb-2">
            <label class="texto" for="nombre_cliente">vendedor: </label>
            <select name="vendedor" class="form-control select2" style="width: 100%;">
                <option value="">Seleccione...</option>
                @foreach($vendedores as $vendedor)
                <option @if(isset($params['vendedor']) and $params['vendedor'] == $vendedor->id_usuariocrea) selected @endif  value="{{$vendedor->id_usuariocrea}}">@if(isset($vendedor->vendedor)){{$vendedor->vendedor->nombre1}} {{$vendedor->vendedor->apellido1}} @endif</option>
                @endforeach
            </select>
        </div>
        @endif
    </div>

    <div class="flex justify-end">
        <button type="submit" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
            <i class="fa fa-search" aria-hidden="true"></i>
            Buscar
        </button>
    </div>
</form>