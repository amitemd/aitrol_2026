@extends('calendario_vacaciones.mantenimiento_vacaciones.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">
<section class="content">
    <div class="box">
        <div class="box-body">
            <form id="form_pro_agrup" method="post" action="{{ route('recursos_humanos.Calendario.descargar_vacaciones')}}">
                {{ csrf_field() }}
                <div class="col-md-8">
                    <h3 class="box-title"> {{trans('nuevos.mantenimiento_vacaciones')}} </h3>
                </div>


                <div>
                    <div class="col-md-12" style="text-align: center">
                        <a href="{{route('recursos_humanos.Calendario.index_calendario_vacaciones')}}" class="btn btn-warning">{{trans('nuevos.visualizar_calendario')}} </a>
                    </div>

                    <div class="col-md-12" style="text-align: left">
                        <a href="{{route('recursos_humanos.Calendario.descargar_plantilla_vacaciones')}}" class="btn btn-info"> Plantilla </a>
                    </div>


                    <div class="col-md-12" style="text-align: right">
                        <button type="submit" class="btn btn-success">Reporte de Vacaciones</button>
                    </div>
                </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group col-md-3">
                    <label class=" control-label col-md-3">Año</label>
                    <div class="form-group col-md-4 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                        <input type="text" name="anio" id="anio" class="form-control autocom input-sm" placeholder="ingrese año" value="@if(isset($anio)){{$anio}}@endif">
                    </div>
                </div>


                <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha" class="form-group col-md-5 col-xs-3">{{trans('winsumos.fecha_desde')}}</label>
                    <div class="col-md-6">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">

                        </div>
                    </div>
                </div>

                <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                    <label for="fecha_hasta" class="form-group col-md-5 col-xs-3">{{trans('winsumos.fecha_hasta')}}</label>
                    <div class="col-md-6">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">

                        </div>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    @php $cantidadSucursales = count($sucursales); @endphp
                    <div class="form-group col-md-5 col-xs-2">
                        <label class="texto" for="mes">{{trans('winsumos.sucursal')}}</label>
                    </div>
                    <div class="form-group col-md-6 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                        <select id="sucursal" name="sucursal" class="form-control">
                            @if($cantidadSucursales > 1)<option value="">Seleccione...</option>@endif
                            @foreach ($sucursales as $sucursal)
                            <option value="{{$sucursal->id}}">{{$sucursal->codigo_sucursal}}-{{$sucursal->nombre_sucursal}}</option>.
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

            <div class="col-md-12">
                <br>
                <div class="form-group col-md-3">
                    <label class="control-label col-md-4">Usuario</label>
                    <div class="form-group col-md-8 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                        <input type="text" name="usuario" id="usuario" class="form-control autocom input-sm" placeholder="Ingrese nombre del Usuario" value="@if(isset($usuario)){{$usuario}}@endif">
                    </div>
                </div>
                <div class="form-group col-md-4">
                    <div class="form-group col-md-5 col-xs-3">
                        <label>Estado Vacaciones</label>
                    </div>
                    <div class="form-group col-md-6 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                        <select class="form-control" name="estado_vacaciones" id="estado_vacaciones">
                            <option @if($estado_vacaciones==1) selected="selected" @endif value="1">Vacaciones Gozadas</option>

                        </select>
                    </div>
                </div>
                <div class="form-group col-md-3">
                    <div class="form-group col-md-6 col-xs-3">
                        <label>Estado empleado</label>
                    </div>
                    <div class="form-group col-md-5 col-xs-3" style="padding-left: 0px;padding-right: 0px;">
                        <select class="form-control" name="estado" id="estado">
                            <option @if($estado==1) selected="selected" @endif value="1">Activo</option>
                            <option @if($estado==0) selected="selected" @endif value="0">Inactivo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-md-2">
                    <div class="col-md-1">
                        <button type="submit" formaction="{{ route('recursos_humanos.Calendario.index_mantenimiento_vacaciones')}}" class="btn btn-primary btn-xl" id="boton_buscar">
                            <span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 16px"> {{trans('winsumos.Buscar')}}&nbsp;</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12" style="text-align: right">
            <a href="{{route('recursos_humanos.calendario.crear')}}" class="btn btn-success">{{trans('nuevos.crear')}}</a>
        </div>
        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
            <div class="row" id="listado_pro_agrup">
                <div class="table-responsive col-md-12">
                    <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info">
                        <thead>
                            <tr>
                                <th> Id. </th>
                                <th>Año</th>
                                <th> Cedula </th>
                                <th>{{trans('nuevos.usuario')}} </th>
                                <th>{{trans('nuevos.empresa')}} - Sucursal </th>
                                <th> Estado empleado  </th>
                                <th>{{trans('nuevos.inicio')}} </th>
                                <th>{{trans('nuevos.fin')}} </th>
                                <th> Estado Vacacion </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($resultados as $value)

                            <tr>
                                <td>{{$value->id}}</td>
                                <td>{{$value->anio}}</td>
                                <td>{{$value->id_usuario}} </td>
                                <td>{{$value->apellido1}} {{$value->apellido2}} {{$value->nombre1}} {{$value->nombre2}} <br> <b> @if(isset($value->observacion)) {{$value->observacion}} @else @endif </b> </td>
                                <td>@if(isset($value->empresa)){{$value->empresa->nombrecomercial}} @else {{$value->nombrecomercial}} @endif <br> @if(isset($value->nombre_sucursal)){{$value->nombre_sucursal}} @else @endif</td>
                                <td>@if($value->estado_nomina == 1) Activo @else Inactivo @endif </td>
                                <td>@if(isset($value->inicio)) {{date('d-m-Y', strtotime($value->inicio))}} @else @endif</td>
                                <td>@if(isset($value->fin)) {{date('d-m-Y', strtotime($value->fin))}} @else @endif</td>
                                <td>@if($value->estado == 1) Vacaciones Gozadas @else  @endif</td>
                                <td>
                                    <a href="{{route('recursos_humanos.Calendario.editar_vacaciones',['id'=>$value->id])}}" class="btn btn-warning"><i class="fa fa-edit"></i></a>
                                    <a onclick="eliminar({{$value->id}})" class="btn btn-danger"><i class="fa fa-trash"></i></a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="row">
                        <div class="col-sm-5">
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('winsumos.mostrando')}} 1 {{trans('winsumos.a')}} {{count($resultados)}} {{trans('winsumos.de')}} {{$resultados->total()}} {{trans('winsumos.registros')}}</div>
                        </div>
                        <div class="col-sm-7">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                {{ $resultados->appends(Request::only(['usuario', 'anio','sucursal', 'estado','fecha','fecha_hasta']))->links() }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        </form>
    </div>

</section>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>
<script>
    $(function() {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha}}',
        });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',
            defaultDate: '{{$fecha_hasta}}',

        });
        $("#fecha").on("dp.change", function(e) {
            //buscar();
        });

        $("#fecha_hasta").on("dp.change", function(e) {
            //buscar();
        });
    });

    function eliminar(id) {
        Swal.fire({
            title: `Estas seguro de eliminar`,
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    type: 'get',
                    url: "{{url('calendario/vacaciones/eliminar/')}}/" + id,
                    dataType: 'json',
                    success: function(data) {
                        console.log('Recargando la página');
                        location.reload();
                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
        });
    }
    document.addEventListener("DOMContentLoaded", function() {

        document.getElementById("usuario").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                document.getElementById("boton_buscar").click();
            }
        });

        document.getElementById("anio").addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                e.preventDefault();
                document.getElementById("boton_buscar").click();
            }
        });
    });

    /*function buscar() {
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }*/
</script>
@endsection