@extends('contable.complaint.base')
@section('action-content')

<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Anulaciones Duplicadas</h3>

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
                        Observación
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Id asiento anulado
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Asiento creado por anulación
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Usuario Crea
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Fecha Crea
                    </th>
                </tr>
            </thead>
            <tbody>
              
                @foreach($logContable as $value)
                @if($value->aux > 1)
                <tr class="bg-white border-b ">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ $value->observacion }}
                    </th>
                    <td class="px-6 py-4">
                        <a style="color:#D1555F; font-weight:bold;" href="{{route('librodiario.edit',['id'=>$value->id_ant])}}" target="_blank">
                            {{ $value->id_ant }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        <a style="color:#D1555F; font-weight:bold;" href="{{route('librodiario.edit',['id'=>$value->id_referencia])}}" target="_blank">
                            {{ $value->id_referencia }}
                        </a>
                    </td>
                    <td class="px-6 py-4">
                        {{ $value->nombre_usuario }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $value->created_at }}
                    </td>
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection