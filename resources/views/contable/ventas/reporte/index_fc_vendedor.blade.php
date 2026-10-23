@extends('contable.ventas.base')
@section('action-content')

<style>
    :root {
        --color: transparent linear-gradient(90deg, #2A79C6 0%, #115596 100%) 0% 0% no-repeat padding-box !important;
    }

    .emitido {
        background-color: #00a65a;
        font-weight: 600;
        color: #ffffff;
    }

    .pendiente {
        background-color: #f39c12;
        font-weight: 600;
        color: #ffffff;
    }

    .anulado {
        background-color: #dd4b39;
        font-weight: 600;
        color: #ffffff;
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

    .table {
        margin-bottom: -10px;
    }

    .table-striped>thead>tr>th>td,
    .table-striped>tbody>tr>th>td,
    .table-striped>tfoot>tr>th>td,
    .table-striped>thead>tr>td,
    .table-striped>tbody>tr>td,
    .table-striped>tfoot>tr>td {
        padding: 0.5px;
        line-height: 1;
    }
</style>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.2/css/jquery.dataTables.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<section class="content">
    <div class="box">
        <div style="background-color: white; border-radius: 8px; padding: 15px">
            <div class="box-header">
                <div class="grid sm:grid-cols-2 grid-cols-1 gap-4">
                    <h3 class="text-3xl font-semibold text-gray-800">Reporte Facturas por Vendedor</h3>

                    <div class="flex justify-end md:flex-row flex-col gap-5 place-items-center">
                        <div class="text-center md:w-auto w-full">

                        </div>
                        <div class="text-center md:w-auto w-full">

                        </div>
                        <div class="text-end md:w-auto w-full">
                        </div>
                    </div>
                </div>

                <div style="background-color: #D6DBDF; border-radius: 5px; margin-bottom: 15px;" class="col-md-12 mt-4">
                    <p style="margin: 0; font-weight:bold; padding:7px 0px; color: black;">Buscador</p>
                </div>
            </div>
            <div class="box-body table-responsive">
                <form action="{{ route('ventas.indexFcVendedor') }}" method="post">
                    {{ csrf_field() }}
                    <div class="col-md-12">
                        <div class="form-group col-md-1 col-xs-2">
                            <label class="texto" for="descripcion">Fecha Desde: </label>
                        </div>
                        <div class="form-group col-md-2 col-12 container-4">
                            <input class="form-control rounded" type="date" id="fecha_desde" name="fecha_desde" value="{{$fecha_desde}}">
                        </div>


                        <div class="form-group col-md-1 col-xs-2">
                            <label class="texto" for="descripcion">Fecha Hasta: </label>
                        </div>
                        <div class="form-group col-md-2 col-12 container-4">
                            <input class="form-control rounded" type="date" id="fecha_hasta" name="fecha_hasta" value="{{$fecha_hasta}}">
                        </div>
                        @if($rolUser !== 17)
                        <div class="form-group col-md-1 col-xs-2">
                            <label class="texto" for="descripcion">Vendedor: </label>
                        </div>
                        <div class="form-group col-md-2 col-12 container-4">
                            <select style="width: 100%;" name="id_vendedor" id="id_vendedor" class="form-control select2_vendedor">
                                <option value="">Seleccione ...</option>
                                @if(!is_null($vendedor))
                                <option selected value="{{$vendedor}}">{{$nombreV->nombre1}} {{$nombreV->apellido1}}</option>
                                @endif
                            </select>
                        </div>
                        @endif

                        <div class="form-group col-md-2 col-12 container-4">
                            <button class="bg-blue-400 py-[6px] px-3 transition-all duration-300  hover:bg-blue-600 text-white rounded w-full md:w-auto" type="submit" id="btnBuscar">
                                <i class="fa fa-search" aria-hidden="true"></i> Buscar
                            </button>
                        </div>
                        <div class="form-group col-md-1 col-12 container-4">
                            <button type="submit" class="btn btn-danger" formaction="{{route('ventas.indexFcVendedor_pdf')}}" formtarget="_blank">
                                <i class="fa fa-file-pdf-o" aria-hidder="true">&nbsp;&nbsp; PDF</i>
                            </button>
                        </div>


                    </div>
                </form>

                <div class="col-md-12 bg-gray-200 p-2 rounded">
                    <p style="margin: 0; font-weight:bold;" class="text-gray-500">Listado Facturas</p>
                </div>
                <div class="col-md-12 mt-5" style="padding: 0px;">
                    <table id="example2" class="display compact " style="font-size: 12px; width: 100%;">
                        <thead>
                            <tr>
                                <th tabindex="0" aria-controls="example2" width="10%">Id</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Fecha</th>
                                <th tabindex="0" aria-controls="example2" width="20%">Cliente</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Comprobante</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Valor Neto</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Iva</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Total</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Responsable</th>
                                <th tabindex="0" aria-controls="example2" width="10%">Accion</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                            $totalVentas = 0;
                            @endphp
                            @foreach ($ventas as $venta)
                            @php
                            $subtotal = round(($venta->subtotal_0 + $venta->subtotal_12) - $venta->descuento,2);
                            $totalVentas += $subtotal;
                            @endphp
                            <tr>
                                <td>{{ $venta->id }}</td>
                                <td>{{ $venta->fecha }}</td>
                                <td>{{ $venta->nombre_cliente }}</td>
                                <td>{{ $venta->nro_comprobante }}</td>
                                <td>$ {{ $subtotal }}</td>
                                <td>$ {{ $venta->impuesto }}</td>
                                <td>$ {{ $venta->total_final }}</td>
                                <td>{{ $venta->vendedor }}</td>
                                <td>
                                    <a href="{{ asset('/') }}api_doc_electronico?opcion=generarPdf&clave={{ $venta->nro_autorizacion }}" class="btn btn-xs " style="text-align: left; color: black; padding: 5px 0px 5px 15px;">
                                        <i class="fa fa-file-pdf-o"></i> Exportar PDF
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                            <tr>
                                <td colspan="3"></td>
                                <td>Total Ventas Netas:</td>
                                <td>$ {{ $totalVentas }}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    $(document).ready(function() {

        $('.select2_vendedor').select2({
            ajax: {
                url: '{{route("clientes.pagarvendedor")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    console.log(data);
                    return {
                        results: data
                    };
                }
            }
        });

        $('#example2').DataTable({
            'paging': false,
            "scrollX": true,
            "scrollY": 450,
            dom: 'lBrtip',
            'lengthChange': false,
            'searching': true,
            'ordering': false,
            responsive: true,
            'info': false,
            'autoWidth': true,


        })

    });
</script>



@endsection