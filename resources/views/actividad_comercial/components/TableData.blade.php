{{--
@if($rolUsuario == 7)
<div class="grid-cards bg-white mt-4 gap-2 place-items-center">
    @foreach($datos as $data)
    <div class="w-[316px] border   border-gray-100  shadow-lg rounded-lg p-4 grid @if($data->prospecto == 1) banda @endif">
        <div class="w-full">
            <div class="border-b-4 border-blue-300">
                <h2 class="font-bold text-gray-800 text-xl">
                    {{ isset($data->cliente) ? $data->cliente->nombre : '' }}
                </h2>
            </div>
            <div class="mt-4">
                <p class="font-semibold text-sm">
                    {{ isset($data->cliente) ? $data->cliente->direccion_representante : '' }}
                </p>
            </div>
            <div class="mt-4">
                <p class="font-semibold text-sm">
                    {{ date('d/m/Y H:m', strtotime($data->fecha_desde))}} - {{ date('d/m/Y H:m', strtotime($data->fecha_hasta))}}
                </p>
            </div>
            <div class="mt-4">
                <p class="font-semibold text-sm underline">{{ isset($data->actividadestado) ? $data->actividadestado->actividad_comercial : ''}}</p>
            </div>
            @include('actividad_comercial.changeEstado')
            <div class="mt-4 w-full">
                <div class="grid-buttons  place-items-center item-center gap-2">
                    <a class="w-full" href="{{ route('actividad_comercial.viewActividad', ['id'=> $data->id]) }}">
                        <button class="border-2 border-blue-500 text-blue-500 hover:text-white hover:bg-blue-500 transition-all duration-500 rounded w-full text-center font-bold py-1 px-2">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </a>
                    @if($data->estado == 1 and is_null($data->estado_transaccion))
                    <button type="button" onclick="deleteActividad(`{{ $data->id }}`)" class="border-2 border-red-500 text-red-500 hover:text-white hover:bg-red-500 text-center font-bold py-1 px-2 transition-all duration-500  rounded w-full">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif

--}}

{{--@if($rolUsuario == 17)--}}

<div class="grid-cards bg-white mt-4 gap-2 place-items-center">
    @foreach($datos as $data)
    <div class="w-[316px] border   border-gray-100  shadow-lg rounded-lg p-4 grid @if($data->prospecto == 1) banda @endif">
        <div class="w-full">
            <div class="border-b-4 border-blue-300">
                <h2 class="font-bold text-gray-800 text-xl">
                    {{ isset($data->cliente) ? $data->cliente->nombre : '' }}
                </h2>
            </div>
            <div class="mt-4">
                <p class="font-semibold text-sm">
                    {{ isset($data->cliente) ? $data->cliente->direccion_representante : '' }}
                </p>
            </div>
            <div class="mt-4">
                <p class="font-semibold text-sm">
                    {{ date('d/m/Y H:m', strtotime($data->fecha_desde))}} - {{ date('d/m/Y H:m', strtotime($data->fecha_hasta))}}
                </p>
            </div>
            @if($rolUsuario != 17)
            <div class="bg-gray-200 text-gray-800 text-sm font-medium me-2 px-2.5 py-0.5 rounded mt-4">
                <span>
                    Responsable:
                </span>
                <br>
                <span class="font-semibold text-md">
                    @if(isset($data->vendedor)) {{ $data->vendedor->nombre1 }} {{ $data->vendedor->apellido1 }} {{ $data->vendedor->apellido2 }} @endif
                </span>
            </div>
            @endif

            <div class="mt-4">
                <p class="font-semibold text-sm underline">{{ isset($data->actividadestado) ? $data->actividadestado->actividad_comercial : ''}}</p>
            </div>

            @include('actividad_comercial.changeEstado')
            <div class="mt-4 w-full">
                <div class="grid-buttons  place-items-center item-center gap-2">
                    <a class="w-full" href="{{ route('actividad_comercial.viewActividad', ['id'=> $data->id]) }}">
                        <button class="border-2 border-blue-500 text-blue-500 hover:text-white hover:bg-blue-500 transition-all duration-500 rounded w-full text-center font-bold py-1 px-2">
                            <i class="fa fa-eye" aria-hidden="true"></i>
                        </button>
                    </a>
                    @if($data->estado == 1 and is_null($data->estado_transaccion))
                    <button type="button" onclick="deleteActividad(`{{ $data->id }}`)" class="border-2 border-red-500 text-red-500 hover:text-white hover:bg-red-500 text-center font-bold py-1 px-2 transition-all duration-500  rounded w-full">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
{{--@endif--}}
