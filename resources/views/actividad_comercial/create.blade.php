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
        <button type="button" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center" data-toggle="modal" data-target="#myModal">
            Crear prospecto
        </button>
        <a href="{{ route('actividad_comercial.index') }}">
            <button type="button" class="bg-red-600 hover:bg-red-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center">
                <i class="fa fa-arrow-left" aria-hidden="true"></i>
                Regresar
            </button>
        </a>
    </div>

    @include('actividad_comercial.modalProspecto')

    <br><br>

    <div class="max-w-5xl mx-auto my-0" id="container">
        <form id="form">
            <div class="grid md:grid-cols-2 gap-4">
                <div class="mb-2">
                    <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
                    <input type="datetime-local" name="fecha_desde" id="fecha_desde" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div class="mb-2">
                    <label for="fecha_hasta" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
                    <input type="datetime-local" name="fecha_hasta" id="fecha_hasta" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div class="mb-2">
                    <label for="cliente" class="block mb-2 text-sm font-medium text-gray-900">Cliente</label>
                    <select name="cliente" id="cliente" required class="select2_nombres_clientes bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 "></select>
                </div>
                <div class="mb-2">
                    <label for="direccion" class="block mb-2 text-sm font-medium text-gray-900">Direccion</label>
                    <input readonly type="text" name="direccion" id="direccion" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
                <div class="mb-2">
                    <label for="actividad" class="block mb-2 text-sm font-medium text-gray-900 ">Actividad</label>
                    <select id="actividad" onchange="changeActividad()" name="actividad" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="" selected>Seleccione</option>
                        @foreach($estados as $estado)
                        <option value="{{$estado->id}}">{{$estado->actividad_comercial}}</option>
                        @endforeach
                    </select>
                </div>
                <div id="otro_container">

                </div>
                <div class="md:col-span-2">
                    <label for="observacion" class="block mb-2 text-sm font-medium text-gray-900">Observacion</label>
                    <textarea id="observacion" name="observacion" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 " placeholder="Breve descripcion de la actividad a realizar..."></textarea>
                </div>
            </div>
        </form>

        <div class="flex justify-center mt-5">
            <button type="button" onclick="store()" class="bg-blue-400 hover:bg-blue-600 px-4 py-2 text-white rounded font-semibold shadow-xl shadow-gray-300 duration-300 transition-all flex gap-2 items-center">
                Guardar
            </button>
        </div>
    </div>

</div>

<script>
    const changeActividad = () => {
        const actividad = document.getElementById('actividad').value;
        if (actividad === '0') {
            $('#otro_container').empty().append(`
                <div class="mb-2">
                    <label for="text_otro" class="block mb-2 text-sm font-medium text-gray-900">Otro</label>
                    <input type="text" name="text_otro" id="text_otro" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                </div>
            `);
        } else {
            $('#otro_container').empty();
        }
    }

    const validate = () => {
        //compare 2 datetime
        const fecha_desde = new Date($('#fecha_desde').val());
        const fecha_hasta = new Date($('#fecha_hasta').val());
        return console.log(fecha_desde.getHours())
        if (fecha_desde > fecha_hasta) {
            alert('La fecha desde no puede ser mayor a la fecha hasta');
            return;
        }
    }

    function obtenerMinutos(hora) {
        var spl = hora.split(":");
        return parseInt(spl[0]) * 60 + parseInt(spl[1]);
    }

    const store = () => {
        $.ajax({
            type: "POST",
            url: "{{ route('actividad_comercial.store') }}",
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            data: $('#form').serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    // return alertas(data.status, data.status, data.message);
                    return Swal.fire({
                        title: "Exito",
                        text: data.message,
                        icon: "success",
                    }).then(() => window.location.href = "{{ route('actividad_comercial.index') }}")
                } else {
                    Swal.fire({
                        title: "Error",
                        text: data.message,
                        icon: "error",
                    })
                }
            },
            error: function(data) {
                console.log(data)
            }

        })
    }
</script>

<script>
    $(document).ready(function() {
        $(".select2_nombres_clientes").each(function() {
            let placeholderText = this.id === 'identificacion_cliente' ?
                '' :
                '';
            let oldp = this.id === 'identificacion_cliente' ? 1 : 0; // 1 cedula 0 nombre

            $(this).select2({
                placeholder: placeholderText,
                allowClear: true,
                ajax: {
                    url: "{{ route('venta.clientesearch') }}",
                    data: function(params) {
                        var query = {
                            search: params.term,
                            type: oldp
                        }
                        return query;
                    },
                    processResults: function(data) {
                        return {
                            results: data
                        };
                    }
                }
            });

            $(this).on('select2:select', function(e) {
                let selectedElement = e.params.data;

                let newOptionN = new Option(selectedElement.nombre, selectedElement.id, true,
                    true);
                let newOptionC = new Option(selectedElement.id, selectedElement.id, true, true);
                let appendo = oldp == 1 ? newOptionN : newOptionC;
                let nombre = oldp == 1 ? 'cliente_nombre' : 'identificacion_cliente';
                document.getElementById("direccion").value = selectedElement.direccion_representante

                $(`#${nombre}`).append(appendo).trigger('change');
                // $("#direccion").val(selectedElement.direccion_representante);
            });
        });
    });
</script>
@endsection