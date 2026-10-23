@extends('insumos.reporte.caducado.base_caducados')
@section('action-content')

<head>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css">
</head>
<style>
    :root {
        --color: transparent linear-gradient(90deg, #2A79C6 0%, #115596 100%) 0% 0% no-repeat padding-box !important;
    }

    .swal2-icon.swal2-warning {
        font-size: 37px !important;
    }

    .swal2-icon-content {
        font-size: 102px;
    }

    .swal2-warning {
        margin: 16px !important;
    }

    .contenedor-valores {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        padding: 5px 20px;
        width: 100%;
        text-align: center;
    }

    .contenedor-valores>p {
        margin: 0px;
        font-weight: 600;
        color: white;
    }

    .grid-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(20em, 1fr));
        gap: 20px;
        margin-top: 0px;
        padding: 0px 10px;
    }

    .grid-buttons {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(5em, 1fr));
        gap: 5px;
        margin-top: 0px;
    }
</style>

<div style="background-color: white; border-radius: 8px; padding: 15px">
    <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
        <h3 class="text-3xl font-semibold text-gray-800">{{trans('contableM.reporte_productos_caducar')}}</h3>

        <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-center md:w-auto w-full">

            </div>
            <div class="text-center md:w-auto w-full">

            </div>
        </div>
    </div>
    <div class="col-md-5 mt-5" style="padding: 0px;">

        <form method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8" action="{{ route('reporte.reporteProdCaducados') }}">
            {{ csrf_field() }}
            <select name="estado" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6">
                <option value="">{{trans('contableM.todos')}}</option>
                <option value="caducado" {{ $estado == 'caducado' ? 'selected' : '' }}>{{trans('contableM.producto_caducado')}}</option>
                <option value="3meses" {{ $estado == '3meses' ? 'selected' : '' }}>{{trans('contableM.prox_3_meses')}}</option>
                <option value="6meses" {{ $estado == '6meses' ? 'selected' : '' }}>{{trans('contableM.prox_6_meses')}}</option>
                <option value="ok" {{ $estado == 'ok' ? 'selected' : '' }}>{{trans('contableM.producto_buen_estado')}}</option>

            </select>
            <button class="btn btn-primary">{{trans('contableM.buscar')}}</button>
        </form>
    </div>

    <div class="col-md-12 mt-5" style="padding: 0px;">
        <table width="100%" class='table table-bordered table-striped table-responsive table-hover dataTable' id="example2">
            <thead>
                <tr>
                    <th>{{trans('contableM.codigo')}}</th>
                    <th>{{trans('contableM.nombre')}}</th>
                    <th>{{trans('contableM.Descripcion')}}</th>
                    <th>{{trans('contableM.serie')}}</th>
                    <th>{{trans('contableM.Pedido')}}</th>
                    <th>{{trans('contableM.Bodega')}}</th>
                    <th>{{trans('contableM.proveedor')}}</th>
                    <th>{{trans('contableM.marca')}}</th>
                    <th>{{trans('contableM.cantidad')}}</th>
                    <th>{{trans('contableM.fecha_vencimiento')}}</th>
                    <th>{{trans('contableM.estado')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $d)
                <tr>
                    <td>{{ $d['codigo'] }}</td>
                    <td>{{ $d['nombre'] }}</td>
                    <td>{{ $d['descripcion'] }}</td>
                    <td>{{ $d['serie'] }}</td>
                    <td>{{ $d['pedido'] }}</td>
                    <td>{{ $d['bodega'] }}</td>
                    <td>{{ $d['proveedor'] }}</td>
                    <td>{{ $d['marca'] }}</td>
                    <td>{{ $d['cantidad'] }}</td>
                    <td>{{ $d['fecha_vence'] }}</td>
                    <td style="background-color: {{ $d['estado_color'] }}; color: {{ $d['estado_color'] == '#ffc107' ? '#000' : '#fff' }}; font-weight: bold; text-align: center;">
                        {{ $d['estado'] }}
                    </td>
                </tr>
                @endforeach
            </tbody>

        </table>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-8">
                    <div class="dataTables_info" id="approved_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} {{1 + (($data->currentPage() - 1) * $data->perPage())}} / {{count($data) + (($data->currentPage() - 1) * $data->perPage())}} de {{$data->total()}} {{trans('contableM.registros')}}</div>
                </div>
                <div class="col-md-4">
                    <div class="dataTables_paginate paging_simple_numbers" id="approved_paginate">
                        {{ $data->appends(Request::only(['id']))->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">

    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': false,
            'lengthChange': false,
            'searching': false,
            'ordering': true,
            'info': false,
            'autoWidth': false,
            responsive: false,
            scrollX: true,
            dom: 'Bfrtip',
            buttons: [{
                    extend: 'excelHtml5',
                    text: '📥 Excel',
                    title: 'Reporte de Productos'
                },
                {
                    extend: 'pdfHtml5',
                    text: '📄 PDF',
                    title: 'Reporte de Productos',
                    orientation: 'landscape',
                    pageSize: 'A4'
                },
                {
                    extend: 'print',
                    text: '🖨 Imprimir',
                    title: 'Reporte de Productos'
                }
            ],
        });
    });
</script>
@endsection