@extends('contable.informe_varios.base')
@section('action-content')
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">

@php
$fecha_desde = date('Y-m') . "-01";
if(isset($request["fecha_desde"])){
$fecha_desde = $request["fecha_desde"];
}
$fecha_hasta = date('Y-m-d');
if(isset($request["fecha_hasta"])){
$fecha_hasta = $request["fecha_hasta"];
}

@endphp

<style>
    .bg-blue-100 {
        --tw-bg-opacity: 1 !important;
        background-color: rgb(219 234 254 / var(--tw-bg-opacity)) !important;
    }
</style>

<div class="w-full bg-white p-5">
    <form id="form" method="GET" action="{{ route('ventas.reporte_varios.ventasUtilidad') }}">
        <div class="w-full rounded border border-gray-100 p-2">
            <div class="grid sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div class="mb-5">
                    <label for="fecha_desde" class="block mb-2 text-sm font-medium text-gray-900">Fecha desde</label>
                    <input type="date" id="fecha_desde" name="fecha_desde" value="{{ $fecha_desde }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@flowbite.com" required>
                </div>
                <div class="mb-5">
                    <label for="fecha_hasta" class="block mb-2 text-sm font-medium text-gray-900">Fecha hasta</label>
                    <input type="date" id="fecha_hasta" name="fecha_hasta" value="{{ $fecha_hasta }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@flowbite.com" required>
                </div>
                <div class="mb-5">
                    <label for="cliente" class="block mb-2 text-sm font-medium text-gray-900">Cliente</label>
                    <select style="width: 100%;" name="id_cliente" id="id_cliente" class="form-control select2_cliente">
                        <option value="">Seleccione...</option>
                        @if(isset($proforma->cliente))
                        <option selected value="{{$proforma->cliente->identificacion}}">{{$proforma->cliente->nombre}}</option>
                        @endif
                    </select>
                </div>
                <div class="mb-5">
                    <label for="estado" class="block mb-2 text-sm font-medium text-gray-900 ">Estado</label>
                    <select id="estado" name="estado" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option @if(isset($request["estado"])) @if($request["estado"]=="" ) selected @endif @endif value="" selected>Seleccione</option>
                        <option @if(isset($request["estado"])) @if($request["estado"]=="1" ) selected @endif @endif value="1">Activo</option>
                        <option @if(isset($request["estado"])) @if($request["estado"]=="0" ) selected @endif @endif value="0">Inactivo</option>
                    </select>

                    <!-- <input type="email" id="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="name@flowbite.com" required> -->
                </div>
            </div>
            <div class="w-full flex justify-end">
                <button type="submit" class="flex gap-2 items-center bg-blue-500 hover:bg-blue-700 text-gray-50 font-semibold transition-all duration-500 px-4 py-2 rounded">
                    <i class="fa fa-search" aria-hidden="true"></i>
                    Buscar
                </button>
            </div>
        </div>
    </form>

    @php
    $total_precio_venta = 0;
    $total_total_venta = 0;
    $total_costo = 0;
    $total_total_costo = 0;
    $total_utilidad = 0;
    @endphp

    <div class="w-full p-3 max-h-[60vh] overflow-y-scroll">
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table id="examples3" class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="px-6 py-3">
                            Fecha
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Nro. Factura
                        </th>
                        <th scope="col" class="px-6 py-3">
                            RUC
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cliente
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Detalle
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Cantidad
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Precio
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total Venta
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Costo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Total Costo
                        </th>
                        <th scope="col" class="px-6 py-3">
                            Utilidad
                        </th>
                    </tr>
                </thead>
                <tbody id="detalles">
                    <!-- Aqui van los detalles -->
                    @foreach ($datas as $data)
                    @php
                    $count_det = 0;
                    @endphp
                    <tr class="bg-blue-100 border-b  hover:bg-blue-200 ">
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{$data["fecha"]}}
                        </td>
                        <td class="px-6 py-4">
                            {{$data["nro_comprobante"]}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{$data["id_cliente"]}}
                        </td>
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{$data["cliente"]}}
                        </td>
                        @foreach($data["detalles"] as $det)

                        @if($count_det != 0)
                        <tr class="">
                            <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                
                            </td>
                            <td class="px-6 py-4">
                                
                            </td>
                            <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                
                            </td>
                            <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                
                            </td>
                        @endif
                        <td scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                            {{ $det["cod_producto"]}} | {{ $det["nombre_producto"]}}
                        </td>
                        <td class="px-6 py-4">
                            {{ $det["cantidad"]}}
                        </td>
                        <td class="px-6 py-4">
                            {{ number_format( $det["precio_venta"], 2, '.', ',') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ number_format( $det["total_venta"], 2, '.', ',') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ number_format( $det["costo"], 2, '.', ',') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ number_format( $det["total_costo"], 2, '.', ',') }}
                        </td>
                        <td class="px-6 py-4">
                            {{ number_format( $det["utilidad"], 2, '.', ',') }}
                        </td>
                    </tr>


                    @php
                    $total_precio_venta += $det["precio_venta"];
                    $total_total_venta += $det["total_venta"];
                    $total_costo += $det["costo"];
                    $total_total_costo += $det["total_costo"];
                    $total_utilidad += $det["utilidad"];
                    $count_det ++;
                    @endphp
                    @endforeach

                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <th class="px-6 py-4 text-right" colspan="6">Total</th>
                        <th class="px-6 py-4"> {{ number_format($total_precio_venta , 2, '.', ',')}}</th>
                        <th class="px-6 py-4"> {{ number_format($total_total_venta , 2, '.', ',')}} </th>
                        <th class="px-6 py-4"> {{ number_format($total_costo, 2, '.', ',')}} </th>
                        <th class="px-6 py-4"> {{ number_format($total_total_costo, 2, '.', ',')}} </th>
                        <th class="px-6 py-4"> {{ number_format($total_utilidad, 2, '.', ',')}} </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js"></script>
<script>
    const reporte = () => {
        $.ajax({
            type: "POST",
            url: "{{ route('ventas.reporte_varios.busquedaFact') }}",
            dataType: "json",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
            },
            data: $("#form").serialize(),
            success: function(data) {
                $("#detalles").empty()
                data.slice(0, 10).map(value => {
                    $("#detalles").append(headFact(value))
                })
            },
            error: function(data) {

            }
        })
    }

    const headFact = data => {

        let detalles = ``;

        data.detalles.map(value => {
            detalles += tableDetalle(value)
        })

        return `
            <tr class="bg-blue-100 border-b  hover:bg-blue-200 ">
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    ${data.fecha}
                </th>   
                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    ${data.cliente}
                </th>
                <td class="px-6 py-4">
                    ${ data.nro_comprobante }
                </td>
                <td class="px-6 py-4">
                    -
                </td>
                <td class="px-6 py-4">
                    -
                </td>
                <td class="px-6 py-4">
                    -
                </td>
                <td class="px-6 py-4">
                    -
                </td>
                <td class="px-6 py-4">
                    -
                </td>
                <td class="px-6 py-4">
                    -
                </td>
                <td class="px-6 py-4 text-right">
                </td>
            </tr> ${detalles}`
    }

    const tableDetalle = data => `
            <tr class="bg-white border-b  hover:bg-gray-50 ">
                <th  scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    ${data.cod_producto}
                </th>
                <th colspan="2" scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                    ${data.nombre_producto}
                </th>
                <td class="px-6 py-4">
                    ${data.cantidad}
                </td>
                <td class="px-6 py-4">
                    ${data.precio_venta}
                </td>
                <td class="px-6 py-4">
                    ${data.total_venta}
                </td>
                <td class="px-6 py-4">
                    ${data.costo}
                </td>
                <td class="px-6 py-4">
                    ${data.total_costo}
                </td>
                <td class="px-6 py-4">
                    ${data.utilidad}
                </td>
                <td class="px-6 py-4 text-right">
                </td>
            </tr>`
    // window.onload = reporte();
</script>

<script>
    $('#examples3').DataTable({
        'paging': false,
        dom: 'Bfrtip',
        'lengthChange': false,
        'searching': false,
        'ordering': false,
        'info': false,
        'autoWidth': true,
        buttons: [{
                extend: 'copyHtml5',
                footer: true
            },
            {
                extend: 'excelHtml5',
                className: "bg-green-500 transition-all duration-500 rounded text-white hover:bg-green-700 px-3 py-1 font-semibold",
                footer: true,
                title: "Reporte costo y utilidad"
            },
            {
                extend: 'csvHtml5',
                className: "bg-green-500 transition-all duration-500 rounded text-white hover:bg-green-700 px-3 py-1 font-semibold",
                footer: true
            },
            {
                extend: 'pdfHtml5',
                className: "bg-red-500 transition-all duration-500 rounded text-white hover:bg-red-700 px-3 py-1 font-semibold",
                orientation: 'landscape',
                pageSize: 'LEGAL',
                footer: true,
                title: "Reporte costo y utilidad",
                customize: function(doc) {
                    doc.styles.title = {
                        color: 'black',
                        fontSize: '17',
                        alignment: 'center'
                    }
                }
            }
        ],
    });
    $('.select2_cliente').select2({
        tags: false
    });
    $('.select2_cliente').select2({
        placeholder: `Seleccione un cliente...`,
        allowClear: true,
        minimumInputLength: 3,
        cache: true,
        ajax: {
            url: `{{ route('insumos.proformaInsumos.buscadorClientes') }}`,
            data: function(params) {
                var query = {
                    search: params.term,
                    type: 'public'
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
</script>

@endsection