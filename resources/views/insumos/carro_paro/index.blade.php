@extends('insumos.carro_paro.base')
@section('action-content')
@php
use Carbon\Carbon;
$rolUsuario = Auth::user()->id_tipo_usuario;
@endphp
<style>
    .table-responsive,
    .box-body,
    .dataTables_wrapper {
        overflow: visible !important;
    }

    td,
    th {
        overflow: visible !important;
    }

    .dropdown-menu {
        z-index: 9999 !important;
        min-width: 140px;
    }

    .dropdown-menu>li>a {
        color: #333 !important;
        white-space: nowrap;
    }

    .dropdown-menu>li>a:hover {
        background-color: #f5f5f5 !important;
        color: #000 !important;
    }

    .bg-danger {
        background-color: #f8d7da !important;
        color: #721c24 !important;
    }

    .bg-warning {
        background-color: #fff3cd !important;
        color: #856404 !important;
    }

    .bg-info {
        background-color: #d1ecf1 !important;
        color: #0c5460 !important;
    }

    .badge-danger {
        background-color: #dc3545 !important;
        color: #fff;
    }

    .badge-warning {
        background-color: #ffc107 !important;
        color: #212529;
    }

    .badge-info {
        background-color: #17a2b8 !important;
        color: #fff;
    }
</style>
<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">{{trans('winsumos_reportes.insumos')}}</a></li>
            <li class="breadcrumb-item"><a href="../">Carro Paro</a></li>
        </ol>
    </nav>
    <div class="box">
        <div class="box-header with-border"
            style="color:black; font-family:'Helvetica general3'; display:flex; align-items:end; justify-content:end; gap:15px;">
            @if(in_array($rolUsuario, array(1,7)) == true)
            <button type="button" class="btn btn-warning" onclick="verReporteDiario()">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i>
                Reporte de uso
            </button>
            @endif
            <a href="{{ route('insumos.carro.paro') }}" class="btn btn-success">
                <i class="fa fa-plus"></i> Agregar Registro
            </a>

        </div>
        <div class="row head-title">
            <div class="col-md-12 cabecera">
                <label class="color_texto" for="title">{{trans('winsumos_reportes.buscador')}}</label>
            </div>
        </div>

        <div class="box-body dobra">
            <form method="post" action="{{ route('insumos_index') }}">
                {{ csrf_field() }}
                <div class="row">
                    <div class="form-group col-md-4">
                        <label for="fecha_d" class="col-md-4 control-label texto">Fecha desde:</label>
                        <div class="col-md-8">
                            <input type="date" name="fecha_d" class="form-control"
                                value="{{ request('fecha_d') ?? Carbon::now()->startOfMonth()->format('Y-m-d') }}">

                        </div>
                    </div>

                    <div class="form-group col-md-4">
                        <label for="fecha_h" class="col-md-4 control-label texto">Fecha hasta:</label>
                        <div class="col-md-8">
                            <input type="date" name="fecha_h" class="form-control"
                                value="{{ request('fecha_h') ?? Carbon::now()->endOfMonth()->format('Y-m-d') }}">
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="responsable" class="col-md-4 control-label texto">Responsable:</label>
                        <div class="col-md-8">
                            <select id="responsable" class="select2 form-control" name="responsable" autocomplete="off">
                                <option value="">-- Seleccione --</option>
                                @foreach($usuarios as $val)
                                <option {{ request('responsable') == $val->id ? 'selected' : '' }} value="{{$val->id}}">
                                    {{$val->nombre1}} {{$val->nombre2}} {{$val->apellido1}} {{$val->apellido2}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>
                <div class="row">
                    <!-- <div class="form-group col-md-4">
                        <label for="opc" class="col-md-4 control-label texto">Tipo</label>
                        <div class="col-md-8">
                            <select name="opc" id="opc" class="form-control select2">
                                <option value="1">
                                    MEDICAMENTOS
                                </option>
                                <option value="2">
                                    INSUMOS
                                </option>
                            </select>
                        </div>
                    </div> -->
                    <div class="form-group  col-md-4">
                        <label for="servicio" class="col-md-4 control-label texto">Servicio</label>
                        <div class="col-md-8">
                            <select id="servicio" class="form-control" name="servicio" autocomplete="off">
                                <option value="">--Seleccione--</option>
                                <option value="1" selected>GASTROENTEROLOGIA</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <button type="submit" class="btn btn-primary" name="buscar" value="buscar" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true"></span> {{trans('winsumos.Buscar')}}</button>
                        <button type="submit" class="btn btn-danger" value="pdf" name="pdf"  onclick="this.form.target='_blank';">
                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i> PDF
                        </button>

                        <button type="submit" class="btn btn-warning" value="excel" name="excel">
                            <i class="fa fa-file-excel-o" aria-hidden="true"></i></i> EXCEL
                        </button>
                    </div>

                </div>
            </form>
        </div>
        <div class="content" id="contenedor">
            <div id="tbl_carro_paro_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row">
                    <div class="table-responsive col-md-12">
                        <table id="tbl_carro_paro" class="table-bordered table-hover dataTable table-striped" role="grid" aria-describedby="tbl_carro_paro_info" style="width: 100%;">
                            <thead>
                                <tr class="well-dark">
                                    <th>Id</th>
                                    <th>Identificación</th>
                                    <th>Responsable</th>
                                    <th>Servicio</th>
                                    <th>Fecha</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            @foreach($insumos as $value)
                            <tr>
                                <td class="text-center">{{ $value->id }}</td>
                                <td class="text-center">{{ $value->responsable_id }}</td>
                                <td class="text-center">{{ $value->usuario->nombre1 }} {{ $value->usuario->nombre2 }} {{ $value->usuario->apellido1 }} {{ $value->usuario->apellido2 }}</td>
                                <td class="text-center">
                                    @if($value->servicio == 1)
                                    GASTROENTEROLOGÍA
                                    @endif</td>
                                <td class="text-center">{{ $value->fecha }}</td>

                                <td class="text-center">
                                    @if($value->tipo == 1)
                                    MEDICAMENTOS
                                    @elseif($value->tipo == 2)
                                    INSUMOS
                                    @endif
                                </td>

                                <td class="text-center">
                                    @if($value->estado == 1)
                                    <i class="fa fa-check-circle text-success" title="Activo"></i> Activo
                                    @else
                                    <i class="fa fa-times-circle text-danger" title="Inactivo"></i> Inactivo
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group dropup">
                                        <button type="button" class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown">
                                            <i class="fa fa-cogs"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-right">
                                            <li>
                                                <a href="{{route('insumos.carro.paro',['id' => $value->id,'fecha'=>$value->fecha])}}">
                                                    <i class="fa fa-eye text-info"></i> Ver
                                                </a>
                                            </li>
                                            <li>
                                                <a href="#" onclick="inactivar({{ $value->id }})">
                                                    <i class="fa fa-ban text-danger"></i> Inactivar
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('wfact_venta.mostrando')}} {{1 + (($insumos->currentPage() - 1) * $insumos->perPage())}} / {{count($insumos) + (($insumos->currentPage() - 1) * $insumos->perPage())}} {{trans('wfact_venta.de')}} {{$insumos->total()}} {{trans('wfact_venta.registros')}}</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $insumos->appends(request()->query())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
</section>
<div class="modal fade" id="modalReporte">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" style="font-weight: bold;">Reporte Diario</h5>
                <button class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="row">
                    <div class="form-group col-md-6">
                        <label for="fecha_desde" class="col-md-4 control-label texto">Desde:</label>
                        <div class="col-md-8">
                            <input type="date" name="fecha_desde" id="fecha_desde" class="form-control"
                                value="{{ Carbon::now()->format('Y-m-d')}}">
                        </div>
                    </div>

                    <div class="form-group col-md-6">
                        <label for="fecha_hasta" class="col-md-4 control-label texto">Hasta:</label>
                        <div class="col-md-8">
                            <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control"
                                value="{{ Carbon::now()->format('Y-m-d') }}">
                        </div>
                    </div>

                </div>
                <div class="row">
                    <div class="col-md-12 text-right">
                        <button type="button" class="btn btn-primary" id="boton_buscar" onclick="verReporteDiario()">
                            <span class="glyphicon glyphicon-search"></span>
                            {{ trans('winsumos.Buscar') }}
                        </button>
                    </div>
                </div>
                <table class="table table-bordered table-hover" id="tablaMedicamentos">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ID Producto</th>
                            <th>Producto</th>
                            <th>Fecha</th>
                            <th>Responsable</th>
                            <th>Cantidad usada</th>
                            <th>Tipo</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-dismiss="modal">
                    Cerrar
                </button>
            </div>

        </div>
    </div>
</div>


<!-- MODAL DE PRODUCTOS PROXIMOS A CADUCAR -->
<script type="text/javascript">
    $(document).ready(function() {
        $(".select2").select2();
        $('#miModal').modal('show');

    });



    const inactivar = (id) => {

        Swal.fire({
            title: `¿Desea Anular el registro?`,
            text: $("#informa").val(),
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'post',
                    url: "{{route('insumos_cambiarEstado')}}",
                    datatype: 'json',
                    headers: {
                        'X-CSRF-TOKEN': $('input[name=_token]').val()
                    },
                    data: {
                        'id': id,
                    },
                    success: function(data) {
                        if (data.status) {
                            Swal.fire({
                                icon: "success",
                                title: "Registro anulado correctamente",
                            });
                            location.reload();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: `Error en el procedo de edición...`,
                            });
                        }
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        })
    }

    function verReporteDiario() {
        $.ajax({
            type: 'post',
            url: "{{route('insumos.consultar_uso')}}",
            datatype: 'json',
            data: {
                fecha_desde: $("#fecha_desde").val(),
                fecha_hasta: $("#fecha_hasta").val(),
            },
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            success: function(data) {
                if (data.status) {
                    $('#modalReporte').modal('show');
                    let html = '';
                    if (data.html && data.html.length > 0) {

                        data.html.forEach(function(item, index) {

                            html += `
                            <tr>
                                <td>${index + 1}</td>
                                <td>${item.id_producto}</td>
                                <td>${item.nombre}</td>
                                <td>${item.fecha}</td>
                                <td>${item.responsable}</td>
                                <td>${item.cantidad_usada}</td>
                                <td>${item.tipo}</td>
                            </tr>
                        `;

                        });
                    } else {
                        html = `
                            <tr>
                                <td colspan="6" class="text-center">
                                    No hay registros el día de hoy
                                </td>
                            </tr>
                        `;
                    }
                    $('#tablaMedicamentos tbody').html(html);
                } else {
                    Swal.fire({
                        icon: "error",
                        title: "Error en el proceso de guardado...",
                        text: `Error: ${data.error} Line: ${data.line}`,
                    });
                }
            },
            error: function(data) {
                console.log(data);
            }
        });
    }
</script>
@endsection