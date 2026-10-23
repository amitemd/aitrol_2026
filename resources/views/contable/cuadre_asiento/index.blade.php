@extends('contable.cuadre_asiento.base')
@section('action-content')
<script src="https://cdn.tailwindcss.com"></script>


<div class="flex justify-center mt-10">
    @include('contable.cuadre_asiento.modalAsiento')
    <div class="w-[90%]">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Id venta
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nro. Comprobante
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Id asiento
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Observacion
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Accion
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($consulta as $value)
                    <tr class="bg-white border-b  hover:bg-gray-50">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            <a target="_blank" class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded" href="{{ url('contable/ventas/editar') }}/{{ $value['id_venta'] }}">
                                {{ $value["id_venta"] }}
                            </a>
                        </th>
                        <td class="px-6 py-4">
                            {{ $value["nro"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $value["total"] }}
                        </td>
                        <td class="px-6 py-4">
                            <a target="_blank" class="bg-pink-100 text-pink-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded" href="{{ url('contable/contabilidad/libro/diario?id=') }}{{ $value['asiento'] }}">
                                {{ $value["asiento"] }}
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-{{$value['color']}}-100 text-{{$value['color']}}-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded">
                                {{ $value["observacion"] }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($value['type'] == 2)
                            <div class="">
                                <button onclick="buscarAsiento(`{{$value['id_venta']}}`)" type="button" class="bg-green-600 hover:bg-green-800 px-4 py-2 text-white rounded font-semibold shadow-md duration-300 transition-all flex gap-2 items-center" data-toggle="modal" data-target="#myModal">
                                    Revisar
                                </button>
                                <div class="flex gap-1 mt-2">
                                    <input type="text" id="id_anulado_{{$value['asiento']}}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Ingrese el asiento de anulacion" />
                                    <button type="button" onclick="guardarAsiento(`{{$value['asiento']}}`)" class="text-lg text-white rounded font-semibold duration-300 transition-all flex gap-2 items-center">
                                        ✅
                                    </button>
                                </div>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>



        <br>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
                <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white  ">
                    Asientos sin modulos
                    <p class="mt-1 text-sm font-normal text-gray-500 ">
                        Los asientos en esta tabla no estan asociados en ningun modulo, puede estar asientos anulados del modulo de ventas
                        primero debe vincular los asientos anulados, se revisaron los modulos, NC, Ventas, INGRESO, RETENCION, VALORES, C. Valores, C.CUENTAS
                    </p>
                </caption>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Asiento
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Observacion
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Valor
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Debe
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Haber
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($asientos as $asiento)
                    <tr class="bg-white border-b  dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            <a target="_blank" class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded" href="{{ url('contable/contabilidad/libro/diario?id=') }}{{ $asiento['id_asiento'] }}">
                                {{ $asiento["id_asiento"] }}
                            </a>
                        </th>
                        <td class="px-6 py-4">
                            {{ date("Y-m-d", strtotime($asiento["fecha_asiento"]))}}
                        </td>
                        <td class="px-6 py-4">
                            {{ $asiento["observacion"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $asiento["valor"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $asiento["debe"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $asiento["haber"] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <br>


        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 ">
                <caption class="p-5 text-lg font-semibold text-left rtl:text-right text-gray-900 bg-white  ">
                    Saldo de facturas descuadrado
                    <p class="mt-1 text-sm font-normal text-gray-500 ">
                       se reviso los modulos que conforman la fac de venta y estan descuadrado los valores, se necesitan revisar mas profundo esos casos
                    </p>
                </caption>
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Venta
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Fecha
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Observacion
                        </th>
                        <th scope="col" class="px-6 py-3">
                            total_final
                        </th>
                        <th scope="col" class="px-6 py-3">
                            calculado
                        </th>
                        <th scope="col" class="px-6 py-3">
                            saldo_contable
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($valores as $val)
                    <tr class="bg-white border-b  dark:border-gray-700">
                        <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap ">
                            <a target="_blank" class="bg-indigo-100 text-indigo-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded" href="{{ url('contable/ventas/editar') }}/{{ $val['id_venta'] }}">
                                {{ $val["id_venta"] }}
                            </a>
                        </th>
                        <td class="px-6 py-4">
                            {{ date("Y-m-d", strtotime($val["fecha"]))}}
                        </td>
                        <td class="px-6 py-4">
                            {{ $val["observacion"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $val["total_final"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $val["calculado"] }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $val["saldo_contable"] }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>


<script>
    const buscarAsiento = (id) => {
        $("#cuerpo_asiento").empty().append(esqueleto)
        $.ajax({
            url: `{{url('new_proforma/compras/insumos/buscarPosibleAsiento')}}/${id}`,
            type: 'GET',
            success: function(data) {
                let tbodyLlenar = ""
                JSON.parse(data)?.map(value => tbodyLlenar += tbody(value))
                $("#cuerpo_asiento").empty().append(tbodyLlenar)
            }
        })
    }

    const tbody = data => `
        <tr class="bg-white border-b  hover:bg-gray-50">
            <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap" >${data.id}</td>
            <td class="px-6 py-4">${data.observacion}</td>
            <td class="px-6 py-4">${data.valor}</td>
        </tr>`

    const esqueleto = () => {
        let esque = "";
        for (let i = 1; i <= 5; i++) {
            esque += `<tr class="bg-white border-b  hover:bg-gray-50 animate-pulse">
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap" >
                            <div class="h-2.5 bg-gray-200 rounded-full w-[90%] mb-4"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-2.5 bg-gray-200 rounded-full w-[90%] mb-4"></div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="h-2.5 bg-gray-200 rounded-full w-[90%] mb-4"></div>
                        </td>
                    </tr>`
        }

        return esque
    }

    const guardarAsiento = (id) => {
        let id_anulado = $("#id_anulado_" + id).val()
        if (id_anulado == "") {
            return alert("Ingrese el asiento de anulacion")
        }

        $.ajax({
            url: `{{url('new_proforma/compras/insumos/guardarAsientoAnulado')}}`,
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