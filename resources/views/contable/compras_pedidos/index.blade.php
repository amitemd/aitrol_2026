@extends('contable.compras_pedidos.base')
@section('action-content')
<style>
    .ui-autocomplete {
        overflow-x: hidden;
        max-height: 200px;
        width: 1px;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        float: left;
        display: none;
        min-width: 160px;
        _width: 160px;
        padding: 4px 0;
        margin: 2px 0 0 0;
        list-style: none;
        background-color: #fff;
        border-color: #ccc;
        border-color: rgba(0, 0, 0, 0.2);
        border-style: solid;
        border-width: 1px;
        -webkit-border-radius: 5px;
        -moz-border-radius: 5px;
        border-radius: 5px;
        -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
        -webkit-background-clip: padding-box;
        -moz-background-clip: padding;
        background-clip: padding-box;
        *border-right-width: 2px;
        *border-bottom-width: 2px;
    }

    .ui-autocomplete {
        z-index: 5000;
    }

    .ui-autocomplete {
        z-index: 999999;
        list-style: none;
        background-color: #FFFFFF;
        width: 300px;
        border: solid 1px #EEE;
        border-radius: 5px;
        padding-left: 10px;
        line-height: 2em;
    }

    .select2 {
        width: 100% !important;
    }
</style>
<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- Ventana modal editar -->
<div class="modal fade" id="modal_devoluciones" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
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
            <li class="breadcrumb-item active" aria-current="page">Registro de Pedidos</li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header header_new">
            <div class="col-md-9">
                <!--<h8 class="box-title size_text">Empleados</h8>-->
                <!--<label class="size_text" for="title">EMPLEADOS</label>-->
                <h3 class="box-title">Pedidos Compras</h3>
            </div>

            <div class="col-md-1 text-right">
                <button onclick="location.href='{{route('contable.compra_pedidos.create')}}'" class="btn btn-success btn-gray">
                    <i class="fa fa-plus-circle" aria-hidden="true"></i> Crear Pedido
                </button>
            </div>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">BUSCADOR DE PEDIDOS</label>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body dobra">
            <form method="POST" id="reporte_master" action="{{ route('contable.compraspedidos.index') }}">
                {{ csrf_field() }}
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="id">Id: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="id" name="id" value="@if(isset($request->id)){{$request['id']}}@endif" placeholder="Ingrese Id..." />

                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="concepto">Concepto: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <input class="form-control" type="text" id="concepto" name="concepto" value="@if(isset($request->concepto)){{$request['concepto']}}@endif" placeholder="Ingrese Asiento..." />
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="nombre_proveedor">Tipo: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">
                    <select class="form-control select2_find_proveedor" name="proveedor" id="proveedor">
                        
                    </select>
                </div>
                <div class="form-group col-md-1 col-xs-2">
                    <label class="texto" for="fecha">Fecha: </label>
                </div>
                <div class="form-group col-md-3 col-xs-10 container-4">

                    <input type="date" name="fecha" class="form-control fecha" id="fecha" value="@if(isset($request)){{$request['fecha']}}@endif">
                </div>
                <div class="col-md-4 col-xs-2 col-xs-10 container-4">
                    <button type="submit" id="buscarEmpleado" class="btn btn-primary btn-gray">
                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span> Buscar
                    </button>
                </div>
            </form>
        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto">LISTADO DE PEDIDOS</label>
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
                                    <table id="tabla" class="table table-hover dataTable" role="grid" aria-describedby="example2_info">
                                        <thead>
                                            <tr class='well-dark'>
                                            <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Id</th>
                                               
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Concepto</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Fecha autorización</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Secuencia Factura</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Serie</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Creado por</th>
                                                <th width="10%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Estado</th>
                                                <th width="15%" tabindex="0" aria-controls="example2" rowspan="1" colspan="1" aria-label="Codigo: activate to sort column ascending">Acción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php 
                                                $contador=0;
                                            @endphp
                                            @foreach ($pedidos as $value)
                                            @if(!is_null($value))
                                            @php
                                            $nombres = Sis_medico\User::where('id',$value->id_usuariocrea)->first();
                                            $veri= Sis_medico\Ct_compras::where('orden_compra','P'.$value->id)->first();
                                           // $permiso_generar= Sis_medico\Inventario::permisos($id_usuario,'GENERAR COMNPROBANTE');
                                            
                                            @endphp
                                            <tr class="well">
                                                <td>{{$value->id}}</td>
                                                <td>{{$value->observacion}}</td>
                                                <td>{{$value->f_autorizacion}}</td>
                                                <td>{{$value->secuencia_factura}}</td>
                                                <td>{{$value->serie}}</td>
                                                <td>{{$nombres->nombre1}} {{$nombres->nombre2}} {{$nombres->apellido1}} {{$nombres->apellido2}}</td>
                                                <td>@if($value->estado == '1') Activo @elseif($value->estado =='0') Anulada @else Activo @endif</td>
                                                <td>
                                                    @if($value->estado!=0)

                                                    <a style="margin-right:1.5px;" onclick="cambiar_estado({{$value->id}})" class="btn btn-danger btn-gray "><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                    <a href="{{ route('contable.compra_pedidos.edit', ['id' => $value->id]) }}" class="btn btn-success btn-gray "><i class="glyphicon glyphicon-eye-open" aria-hidden="true"></i></a>
                                                    <a href="{{route('contable.timeline',['id' => $value->id])}}" target="_blank" class="btn btn-primary btn-gray"> <i class="fa fa-calendar"></i> </a>
                                                    @if($value->aprobar_pedido==1  and $permisos['COMPRA']  )
                                                    <a href="{{ route('contable.pedido.generar', ['id' => $value->id])}}" class="btn btn-success btn-gray "><i class="fa fa-file" aria-hidden="true"></i></a>
                                                    @endif
                                                    @if($value->aprobado==0 and $value->aprobar_pedido==0 and $permisos['APROBACION PEDIDO'])
                                                    <button class="btn btn-primary btn-gray" title="Aprobar" onclick="aprobar('{{$value->id}}')"> <i class="fa fa-check "></i> </button>
                                                    @endif
                                                    @if($value->aprobado==2 and $permisos['APROBACION DE PAGO'])
                                                    <button class="btn btn-primary btn-gray" type="button" onclick="aprobar2('{{$value->id}}')"><i class="fa fa-check "></i> </button>
                                                    @endif
                                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                    @endif
                                                    @if($value->aprobar_pedido != 1 and $permisos['APROBACION PEDIDO'])
                                                    <a href="{{route('contable.compra_pedidos.check', ['id'=> $value->id])}}" class="btn btn-success btn-gray "><i class="fa fa-check-square-o" aria-hidden="true"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                            @php 
                                                $contador++;
                                            @endphp
                                            @endif
                                            @endforeach
                                        </tbody>
                                        <tfoot>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($pedidos->currentPage() - 1) * $pedidos->perPage())}} / {{count($pedidos) + (($pedidos->currentPage() - 1) * $pedidos->perPage())}} de {{$pedidos->total()}} registros</div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $pedidos->appends(request()->query())->links()  }}
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
<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#tabla').DataTable({
            'paging': false,
            'lengthChange': true,
            'searching': false,
            'ordering': false,
            'info': false,
            'autoWidth': false,
            'sInfoEmpty': true,
            'sInfoFiltered': true,
            'language': {
                "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
            }
        });
    });
    $('.select2').select2({
        tags: false
    });

    function cambiar_estado(id) {
        Swal.fire({
            title: 'Quiere cambiar de estado?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Si`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'post',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable.cambio_estado.edit')}}",
                    dataType: "json",
                    data: {
                        'id': id,
                    },
                    success: function(data) {
                        if (data == 'ok') {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Correcto',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            location.reload();
                        }
                    }
                });
            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }
        })


    }
    function aprobar(id){
        Swal.fire({
            title: 'Desea Aprobar este pedido?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Si`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable.pedido.aprobar')}}",
                    dataType: "json",
                    data: {
                        'id': id,
                    },
                    success: function(data) {
                        if (data == 'ok') {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Correcto',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            location.reload();
                        }
                    }
                });
            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }
        })
    }
    function aprobar2(id){
        Swal.fire({
            title: 'Desea Aprobar el pago?',
            showDenyButton: true,
            showCancelButton: true,
            confirmButtonText: `Si`,
            denyButtonText: `No`,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    url: "{{route('contable.pedido.aprobar_factura')}}",
                    dataType: "json",
                    data: {
                        'id': id,
                    },
                    success: function(data) {
                        if (data == 'ok') {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: 'Correcto',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            location.reload();
                        }
                    }
                });
            } else if (result.isDenied) {
                Swal.fire('Changes are not saved', '', 'info')
            }
        })
    }


    $('.select2_find_proveedor').select2({
        placeholder: "Escriba el nombre del proveedor",
         allowClear: true,
        ajax: {
            url: '{{route("comprapedido.proveedorsearch")}}',
            data: function (params) {
            var query = {
                search: params.term,
                type: 'public'
            }
            return query;
            },
            processResults: function (data) {
                // Transforms the top-level key of the response object from 'items' to 'results'
                console.log(data);
                return {
                    results: data
                };
            }
        }
    });

</script>
@endsection
