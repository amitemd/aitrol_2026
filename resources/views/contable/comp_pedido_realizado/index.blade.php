@extends('contable.comp_pedido_realizado.base')
@section('action-content')

<!-- Ventana modal editar -->
<div class="modal fade" id="seguimiento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">

        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Contable</a></li>
            <li class="breadcrumb-item"><a href="#">Compras</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pedidos Realizados</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <h3 class="box-title">Lista de Producto</h3>
            </div>
            <div class="col-md-2 ">
                <a class="btn btn-primary" href="{{route('compra.crear_bodega_producto')}}" style="width: 100%;">Ingreso de Pedido</a>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR DE COMPRAS</label>
            </div>
        </div>
     
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" action="{{ route('compras.pedidos') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="numerodepedido">Pedido: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="numerodepedido" name="numerodepedido" value="@if(isset($searchingVals)){{$searchingVals['numerodepedido']}}@endif" autocomplete="off" placeholder="Ingrese codigo..." />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="proveedor">Proveedor:</label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="proveedor" name="proveedor" value="@if(isset($searchingVals)){{$searchingVals['id_proveedor']}}@endif" autocomplete="off" placeholder="Ingrese el nombre..." />
                </div>
                <div class="col-xs-2">
                    <button type="submit" id="buscarCodigo" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                </div>
                
            </form>

            <!-- <form>
                <div class="form-group">
                    <label for="numero_pedido" class="col-sm-2 ">Numero de Pedidos</label>

                    <div class="row col-sm-9">
                        <input type="text" class="col-sm-3" id="numero_pedido" name="numero_pedido" placeholder="NÚMERO DE PEDIDOS">
                        <button type="submit" class="btn btn-sm btn-primary " style="margin-left: 10px;" ><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar</button>
                    </div>
                </div>
            </form> -->

        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO DE PEDIDOS REALIZADOS</label>
            </div>
        </div>
        <div class="box-body dobra">
            <div class="form-group col-md-12">
                <div class="form-row">
                    <div id="resultados">
                    </div>
                    <div id="contenedor">
                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                            <div class="row">
                                <div class="table-responsive col-md-12">

                                    <table id="example2" class="table table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Proveedor</th>
                                                <th>Numero de Pedido</th>
                                                <th>Numero de Factura</th>
                                                <th>Realizado por</th>
                                                <th>Items Totales</th>
                                                <th>Total de Productos Restantes</th>
                                                <th>Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                            $i=0;
                                            @endphp
                                            @foreach ($pedidos as $value)
                                            <tr>
                                                <td>{{ $value->created_at }}</td>
                                                <td>{{ $value->nombrecomercial }}</td>
                                                <td>{{ $value->pedido }}</td>
                                                <td>{{ $value->factura }}</td>
                                                <td>{{ $value->nombre1}} {{ $value->apellido1}}</td>
                                                <td>@if($cantidades[$i][0] != null){{$cantidades[$i][0]}}@else 0 @endif</td>
                                                <td>@if($cantidades[$i][1] != null){{$cantidades[$i][1]}}@else 0 @endif</td>
                                                <td>
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    <a href="{{ route('pedido.seguimiento', ['id' => $value->id]) }}" data-toggle="modal" data-target="#seguimiento" class="btn btn-warning btn-gray ">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <!-- <a class="btn btn-success"> Ver Pedido</a> -->
                                                    <!-- <a href="{{ route('ingreso.editar_pedido', ['id' => $value->id]) }}" class="btn btn-success col-md-6 col-xs-6 btn-margin">
                                                    Editar Pedido
                                                    </a> -->
                                                </td>
                                            </tr>
                                            @php
                                            $i =$i+1;
                                            @endphp
                                            @endforeach
                                        </tbody>
                                        <!-- <tfoot>
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Proveedor</th>
                                                <th>Numero de Pedido</th>
                                                <th>Numero de Factura</th>
                                                <th>Realizado por</th>
                                                <th>Items Totales</th>
                                                <th>Total de Productos Restantes</th>
                                            </tr>
                                        </tfoot> -->
                                    </table>

                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($pedidos->currentPage() - 1) * $pedidos->perPage())}} / {{count($pedidos) + (($pedidos->currentPage() - 1) * $pedidos->perPage())}} de {{$pedidos->total()}} registros</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $pedidos->appends(Request::only(['proveedor', 'observacion', 'secuencia_factura','ct_c.tipo_comprobante','fecha','numerodepedido']))->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="{{ asset ("/js/jquery-ui.js")}}"></script>

<script type="text/javascript">
    $('#seguimiento').on('hidden.bs.modal', function() {
        $(this).removeData('bs.modal');
    });

    $(document).ready(function() {
        $('#example2').DataTable({
            'paging': false,
            'lengthChange': true,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': true,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });

    });
</script>
@endsection