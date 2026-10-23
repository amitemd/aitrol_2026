@extends('hc4.ordenes.orden_ingresada_formato012.base')
@section('action-content')
<link rel="stylesheet" href="{{ asset("/css/bootstrap-datetimepicker.css")}}">

<!-- <style>
    /* unvisited link */
    a:link {
        color: black;
    }

    /* visited link */
    a:visited {
        color: lightgreen;
    }

    /* mouse over link */
    a:hover {
        color: blue;
    }
    button{
    width: 100%;
    }
</style> -->

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12 col-xs-12">
            <div class="box box-primary">
                <div class="box-header">


                    <div class="box-body">
                        <form method="post" action="{{ route('formato012.search') }}">
                        {{ csrf_field() }}
                            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                                <label for="fecha" class="col-md-3 control-label">Desde</label>
                                <div class="col-md-9">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control input-sm" name="fecha" id="fecha" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha').value = ''; buscar();"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                                <label for="fecha_hasta" class="col-md-3 control-label">Hasta</label>
                                <div class="col-md-9">
                                    <div class="input-group date">
                                        <div class="input-group-addon">
                                            <i class="fa fa-calendar"></i>
                                        </div>
                                        <input type="text" class="form-control input-sm" name="fecha_hasta" id="fecha_hasta" autocomplete="off">
                                        <div class="input-group-addon">
                                            <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('fecha_hasta').value = ''; buscar();"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>                            

                            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                                <label for="cedula" class="col-md-3 control-label">Cédula</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                    <input value="@if($cedula!=''){{$cedula}}@endif" type="text" class="form-control input-sm" name="cedula" id="cedula" placeholder="Cédula" >
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('cedula').value = ''; buscar();"></i>
                                    </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                                <label for="nombres" class="col-md-3 control-label">Paciente</label>
                                <div class="col-md-9">
                                    <div class="input-group">
                                    <input value="@if($nombres!=''){{$nombres}}@endif" type="text" class="form-control input-sm" name="nombres" id="nombres" placeholder="Nombres y Apellidos" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                                    <div class="input-group-addon">
                                        <i class="glyphicon glyphicon-remove-circle" onclick="document.getElementById('nombres').value = '';"></i>
                                    </div>
                                    </div>
                                </div>
                            </div>


                            <div class="form-group col-md-4 col-xs-6" style="padding-left: 0px;padding-right: 0px;">
                                <label for="id_doctor_firma" class="col-md-3 control-label">Doctor</label>
                                <div class="col-md-9">
                                    <select class="form-control input-sm" name="id_doctor_firma" id="id_doctor_firma">
                                    <option value="">Seleccione ...</option>
                                    @foreach($doctores as $doctor)
                                        <option @if($doctor->id=='1307189140') style="color:red;" @endif @if($doctor->id==$id_doctor1) selected @endif value="{{$doctor->id}}">{{$doctor->apellido1}} {{$doctor->apellido2}} {{$doctor->nombre1}} </option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-group col-md-1 col-xs-4" >
                                <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                                <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </div>

                        </form>


                        <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap ">
                            <div class="table-responsive col-md-12 col-xs-12">
                                <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="font-size: 12px;">
                                    <thead>
                                        <tr>
                                            <th>Fecha creación</th>
                                            <th>Fecha de Orden</th>
                                            <th>Cédula</th>
                                            <th>Paciente</th>
                                            <th>Empresa</th>
                                            <th>Doctor</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($ordenes_012 as $item)
                                        <tr>
                                            <td>{{ $item->created_at }}</td>
                                            <td>{{ $item->fecha_orden }}</td>
                                            <td>{{ $item->id_paciente }}</td>
                                            <td>{{ $item->pnombre1 }} {{ $item->papellido1 }}</td>
                                            <td>{{ $item->enombre }}</td>
                                            <td>Dr. {{ $item->dnombre1 }} {{ $item->dapellido1 }}</td>
                                            <th>
                                                <a href="{{ route('orden_012.editar',['id' => $item->id]) }}" class="btn btn-sm btn-primary"><i class="fa fa-pencil-square-o"></i>Editar</a>
                                                <a href="{{ route('orden_012.imprimir_012_excel',['id' => $item->id])}}" class="btn btn-sm btn-success"><i class="fa fa-download" aria-hidden="true"></i> Descargar</a>
                                            </th>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-md-5 col-xs-12">
                            <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando  registros</div>
                        </div>

                        <div class="col-md-7 col-xs-12">
                            <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                               
                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>


<script src="{{ asset ("/plugins/datetimepicker/bootstrap-material-datetimepicker.js") }}"></script>
<script src="{{ asset ("/js/bootstrap-datetimepicker.js") }}"></script>

<script type="text/javascript">

    $(function () {
        $('#fecha').datetimepicker({
            format: 'YYYY/MM/DD',

            defaultDate: '{{$fecha}}',

            });
        $('#fecha_hasta').datetimepicker({
            format: 'YYYY/MM/DD',

            defaultDate: '{{$fecha_hasta}}',

            });
        $("#fecha").on("dp.change", function (e) {
            buscar();
        });

            $("#fecha_hasta").on("dp.change", function (e) {
            buscar();
        });
    });

    $('#example2').DataTable({
        'paging'      : false,
        'lengthChange': false,
        'searching'   : false,
        'ordering'    : true,
        'info'        : false,
        'autoWidth'   : false,
        'order'       : [[ 1, "asc" ]]
    });

    function buscar(){
        var obj = document.getElementById("boton_buscar");
        obj.click();
    }
</script>

@endsection