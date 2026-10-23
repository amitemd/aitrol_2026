@extends('contable.complaint.base')
@section('action-content')
<div class="modal fade" id="verdepreciacion" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Asientos Compras con problemas</h3>

        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-end md:w-auto w-full">

            </div>
        </div>
    </div>
</div>
<div class="bg-white w-full p-4 rounded">
    <div class="box-body dobra">
    </div>
    <br>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3" width="30%">
                        Id Compra
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Valor
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Id asiento
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Observacion
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Acción
                    </th>
                </tr>
            </thead>
            <tbody>

                @foreach($data as $value)
                <tr class="bg-white border-b ">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        <a style="color:#3840DD; font-weight:bold;" href="{{route('compra_editar',['id'=>$value['id_compra']])}}" target="_blank">
                            {{ $value['id_compra'] }}
                        </a>
                    </th>
                    <td class="px-6 py-4">
                        {{ $value['total'] }}
                    </td>
                    <td class="px-6 py-4">
                        <a style="color:#D1555F; font-weight:bold;" href="{{route('librodiario.edit',['id'=>$value['id_asiento']])}}" target="_blank">
                            {{ $value['id_asiento'] }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        {{ $value['msj'] }}
                    </td>
                    <td>
                        @if($value['tipo'] == 2)
                        <a class="btn btn-info " data-remote="{{ route('importaciones.buscar_asientos_compras',[$value['id_compra']])}}" data-toggle="modal" data-target="#verdepreciacion"> <i class="fa fa-eye"></i> Revisar</a>

                        <div class="flex gap-1 mt-2">
                            <input type="text" id="id_anulado_{{$value['id_asiento']}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Ingrese el asiento de anulacion" />
                            <button type="button" onclick="guardarAsiento(`{{$value['id_asiento']}}`)" class="text-lg text-white rounded font-semibold duration-300 transition-all flex gap-2 items-center">
                                ✅
                            </button>
                        </div>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
    $('#verdepreciacion').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    const guardarAsiento = (id) => {
        let id_anulado = $("#id_anulado_" + id).val()
        if (id_anulado == "") {
            return alert("Ingrese el asiento de anulacion")
        }

        $.ajax({
            url: `{{url('contable/guardarAsientoAnulado')}}`,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': `{{ csrf_token() }}`,
            },
            data: {
                id_asiento: id,
                id_asiento_elegido: id_anulado
            },
            success: function(data) {
                console.log(data)
                if (data.status == "success") {
                    Swal.fire({
                        icon: 'success',
                        title: `{{trans('turnerof.guardadoexito')}}`,
                        timer: 1500
                    })

                    setTimeout(() => {
                        location.reload()
                    }, 1500);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: data.message,
                    })
                }

            }
        })

    }
</script>
@endsection