@extends('actividad_comercial.base')
@section('action-content')
<style>
    .select2-selection {
        border-radius: 0.5rem !important;
        height: 40px !important;
    }

    .select2-container--default {
        width: 100% !important;
    }
</style>

<div class="p-4 rounded-lg bg-white mt-4">
    <div class="flex justify-between">
        <h1 class="font-semibold text-gray-600 text-xl">Creación actividad comercial</h1>
        <a href="{{ route('actividad_comercial.index') }}">
            <button type="button" class="bg-red-600 hover:bg-red-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Regresar
            </button>
        </a>
    </div>

    <br><br>

    <div class="max-w-5xl mx-auto my-0">
        <div class="grid md:grid-cols-2 gap-4">
            <div class="mb-2">
                <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
                <input type="datetime-local" value="{{ $actividad->fecha_desde  }}" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-2">
                <label for="fecha_hasta" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
                <input type="datetime-local" disabled value="{{ $actividad->fecha_hasta  }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-2">
                <label for="cliente" class="block mb-2 text-sm font-medium text-gray-900">Cliente</label>
                <input type="text" disabled value="{{  isset($actividad->cliente) ? $actividad->cliente->nombre : 'N/A' }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-2">
                <label for="direccion" class="block mb-2 text-sm font-medium text-gray-900">Direccion</label>
                <input readonly type="text" value="{{  isset($actividad->cliente) ? $actividad->cliente->direccion_representante : 'N/A' }}" disabled class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <div class="mb-2">
                <label for="actividad" class="block mb-2 text-sm font-medium text-gray-900 ">Actividad</label>
                <input type="text" disabled value="Proforma" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
            </div>
            <!-- Si es otros aqui va -->
            <div>

            </div>
            <!-- Si es otro aqui va -->
            <div class="md:col-span-2">
                <label for="observacion" class="block mb-2 text-sm font-medium text-gray-900">Observacion</label>
                <textarea disabled rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{ $actividad->observacion }}</textarea>
            </div>
        </div>

        @if($actividad->estado == 0)
        <div class="flex gap-2 mt-4 items-center">
            <div class="h-4 w-4 rounded-full bg-red-500"></div>
            <p class="font-semibold text-sm">Eliminado</p>
        </div>
        @elseif(is_null($actividad->estado_transaccion))
        <fieldset class="mt-4">
            <legend class="font-semibold text-gray-500 m-0 text-sm">Seleccione una actividad a realizar</legend>
            <div class="grid gap-3 p-4">
                <div class="">
                    <label for="estadosTipo" class="block mb-2 text-sm font-medium text-gray-900 ">Seleccione un tipo</label>
                    <select onchange="handleChangeEstado(this)" id="estadosTipo" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 ">
                        <option selected>Seleccione...</option>
                        <option value="2">Reagendado</option>
                        <option value="1">Aceptado</option>
                        <option value="0">Cancelado</option>
                    </select>
                </div>
                <div class="contenedor_text">
                    <div class="form-group m-0">
                        <textarea id="justificacion" readonly name="justificacion" rows="4" placeholder="Escribir observacion..." class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"></textarea>
                    </div>
                </div>
                <div class="flex justify-center">
                    <button type="button" onclick="storeEstadoactividad()" class="ml-2 border-2 bg-blue-500 text-white hover:text-white hover:bg-blue-700 text-center font-bold py-2 px-4 transition-all duration-500 rounded-md">
                        Guardar
                    </button>
                </div>

            </div>
        </fieldset>
        @elseif($actividad->estado_transaccion == 2)
        <div class="flex gap-2 mt-4 items-center">
            <div class="h-4 w-4 rounded-full bg-orange-500"></div>
            <p class="font-semibold text-sm">Reagendado</p>
        </div>
        <textarea disabled rows="4" class="mt-3 block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{$actividad->justificacion}}</textarea>

        @elseif($actividad->estado_transaccion == 1)
        <div class="flex gap-2 mt-4 items-center">
            <div class="h-4 w-4 rounded-full bg-green-500"></div>
            <p class="font-semibold text-sm">Aceptado</p>
        </div>
        @elseif($actividad->estado_transaccion == 0)
        <div class="flex gap-2 mt-4 items-center">
            <div class="h-4 w-4 rounded-full bg-red-500"></div>
            <p class="font-semibold text-sm">Cancelado</p>
        </div>
        <textarea disabled rows="4" class="mt-3 block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500">{{$actividad->justificacion}}</textarea>
        @endif
    </div>
</div>
@if(is_null($actividad->estado_transaccion))
<script>
    const handleChangeEstado = (e) => {
        if (e.value == 1) {
            $('#justificacion').attr("readonly", true);
        } else {
            $('#justificacion').attr("readonly", false);
        }


    }

    const storeEstadoactividad = () => {
        $.ajax({
            type: "POST",
            url: "{{ route('actividad_comercial.storeEstado') }}",
            data: {
                "id_actividad": `{{ $actividad->id }}`,
                "justificacion": $('#justificacion').val(),
                "estado_transaccion": $("#estadosTipo").val(),
            },
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            success: function(data) {
                if (data.status == 'success') {
                    return Swal.fire({
                        icon: 'success',
                        title: "Exito...",
                        text: data.message,
                    }).then(function() {
                        return location.reload();
                    })
                }

                return Swal.fire({
                    icon: data.status,
                    title: data.status.toUppercase(),
                    text: data.message,
                })

            },
            error: function(data) {
                console.log(data)
            }
        })
    }
</script>
@endif

@endsection