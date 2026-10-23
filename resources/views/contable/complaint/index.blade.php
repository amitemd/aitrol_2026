@extends('contable.complaint.base')
@section('action-content')

<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">Listado Reporte Complaint</h3>
        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
            <div class="text-center md:w-auto w-full">
            </div>
            <div class="text-center md:w-auto w-full">
            </div>
            <div class="text-end md:w-auto w-full">
                <a id="formulario" href="{{ route('formulario.complaint') }}" target="_blank" class="bg-blue-400 py-[6px] px-3 transition-all duration-300  hover:bg-blue-600 text-white rounded w-full md:w-auto">
                    Crear
                </a>
            </div>
        </div>
    </div>
    <div style="background-color: #D6DBDF; border-radius: 5px; margin-bottom: 15px;" class="col-md-12 mt-4">
        <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Buscador</p>
    </div>
    <form method="POST" action="{{route('complaint.buscador')}}" id="complaint_buscador">
        {{ csrf_field() }}
        <div class="col-md-12">
            <div class="form-group col-md-2 col-xs-6">
                <label for="fecha" class="col-md-12 control-label">{{trans('laboratorio.desde')}}:</label>
                <div class="col-md-12">
                    <input type="date" class="form-control rounded" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{$fecha_desde}}">
                </div>
            </div>
            <div class="form-group col-md-2 col-xs-6">
                <label for="fecha_hasta" class="col-md-12 control-label">{{trans('laboratorio.hasta')}}:</label>
                <div class="col-md-12">
                    <input type="date" class="form-control rounded" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{$fecha_hasta}}">
                </div>
            </div>
            @if($rolUser !== 17)
            <div class="form-group col-md-4 col-xs-6">
                <label class="col-md-12 control-label" for="id_vendedor">Vendedor: </label>
                <div class="col-md-12">
                    <select name="id_vendedor" id="id_vendedor" class="form-control select2" style="width: 100%;">
                        <option value=""></option>
                        @foreach ($data as $ven)
                        @php
                        $nombre = $ven->user_crea->nombre1. ' '.$ven->user_crea->nombre2. ' '.$ven->user_crea->apellido1. ' '.$ven->user_crea->apellido2;
                        @endphp
                        <option value="{{$ven->user_crea->id}}" {{ $ven->user_crea->id == $request->id_vendedor ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group col-md-2 col-xs-6">
                <div class="col-md-12">
                    <button type="submit" class="bg-blue-400 py-[6px] px-3 transition-all duration-300  hover:bg-blue-600 text-white rounded w-full md:w-auto" id="boton_buscar">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('laboratorio.buscar')}}
                    </button>
                </div>
            </div>
            @endif
        </div>
    </form>
</div>
<div class="bg-white w-full p-4 rounded">
    <div class="box-body dobra">
    </div>
    <br>
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
        <table class="w-full text-sm text-left rtl:text-right text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                <tr>
                    <th scope="col" class="px-6 py-3">
                        Nombre
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Medico
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Lote
                    </th>
                    <th scope="col" class="px-6 py-3">
                        File
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Responsable
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Action
                    </th>
                    <th scope="col" class="px-6 py-3">
                        Pdf
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach($complaint as $value)
                <tr class="bg-white border-b">
                    <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ $value->nombre }}
                    </th>
                    <td class="px-6 py-4">
                        {{ $value->medico }}
                    </td>
                    <td class="px-6 py-4">
                        {{ $value->numero_lote }}
                    </td>
                    <td class="px-6 py-4">
                        <img loading="lazy" class="w-[50px]" src="{{ asset('storage/'.$value->files) }}" alt="">
                    </td>
                    <td class="px-6 py-4">
                        {{ $value->user_crea->nombre1.' '.$value->user_crea->apellido1 }}
                    </td>
                    <td class="px-6 py-4">
                        <a href="{{ route('complaint.show', ['id' => $value->id])}}" class="font-medium text-blue-600 hover:underline"><i class="fa fa-eye" aria-hidden="true">&nbsp;Ver</i></a>
                    </td>
                    <td class="px-6 py-4">
                        <a id="formulario" href="{{ route('formulario.complaint_pdf', ['id' => $value->id]) }}" target="_blank" type="button" class="btn btn-danger" style="color: #fff; background-color: #d9534f; border-color: #d9534f;">
                            <i class="fa fa-file-pdf-o">&nbsp;&nbsp; PDF</i>
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row">
            <div class="col-sm-5">
                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($complaint->currentPage() - 1) * $complaint->perPage())}} / {{count($complaint) + (($complaint->currentPage() - 1) * $complaint->perPage())}} {{trans('laboratorio.de')}} {{$complaint->total()}} {{trans('contableM.registros')}}</div>
            </div>
            <div class="col-sm-7">
                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                    {{ $complaint->appends(Request::only(['id_vendedor', 'fecha_desde','fecha_hasta']))->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#id_vendedor').select2();
    });
</script>
@endsection