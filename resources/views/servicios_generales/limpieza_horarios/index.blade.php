@extends('servicios_generales.limpieza_horarios.base')
@section('action-content')
@php $t = date('Y-m-d'); @endphp
<style type="text/css">
    table {
        border: none;
        border-collapse: collapse;
    }
</style>
<div class="modal fade" id="agregar_datos" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="modal_editar" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<div class="modal fade" id="obsefinal" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-md" role="document">
        <div class="modal-content">
        </div>
    </div>
</div>
<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-12">
                <div class="col-md-6">
                    <h4 >Registro de Limpieza y Desinfección de Salas</h4>
                </div>
                <div class="col-md-2">
                <a href="{{route('mantenimientohorario.modaleditar')}}" class="btn btn-primary" data-toggle="modal" data-target="#modal_editar"> <i class="fa fa-edit" aria-hidden="true"></i> Editar</a>
                </div>
                <div class="col-md-2">
                <a href="{{route('mantenimientohorario.registrar')}}" data-toggle="modal" data-target="#agregar_datos" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Agregar</a>
                </div>
                <div class="col-md-2">  
                    <a type="button" href="{{route('mantenimientohorario.reporte')}}" class="btn btn-primary"><i class="fa fa-download" aria-hidden="true"></i> Reporte</a>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <form method="POST" id="form" action="{{route('mantenimientohorario.buscar')}}">
                            {{ csrf_field() }}
                            <div class="form-group col-md-1">
                                <label for="sala" class="col-md-4 texto">Sala</label>
                            </div>
                            <div class="form-group col-md-3">
                                <select name="id_sala" id="id_sala" class="form-control sala" value="@if(isset($searchingVals)){{$searchingVals['id_sala']}}@endif">
                                    <option value="">Seleccione</option>
                                    @foreach($sala as $val)
                                    <option value="{{$val->id}}">{{$val->nombre_sala}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="estado" class="col-md-4 texto">Estado</label>
                            </div>
                            <div class="form-group col-md-3">
                                <select name="estado" id="estado" class="form-control" value="@if(isset($searchingVals)){{$searchingVals['estado']}}@endif">
                                    <option value="">Seleccione</option>
                                    <option value="0">Completada</option>
                                </select>
                            </div>
                            <div class="form-group col-md-1">
                                <label for="fecha" class="col-md-4 texto">Fecha</label>
                            </div>
                            <div class="form-group col-md-3">
                                <input type="date" name="fecha" id="fecha" class="form-control" value="<?php echo date("Y-m-d");?>">
                            </div>
                            <div class="form-group col-md-12">
                                <button type="submit" id="buscar" class="btn btn-primary">BUSCAR</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap t9">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Area</th>
                                    <th>Observaciones</th>
                                    <th>Frecuencia1</th>
                                    <th>Frecuencia2</th>
                                    <th>Frecuencia3</th>
                                    <th>Frecuencia4</th>
                                    <th>Frecuencia5</th>
                                    <th>Frecuencia6</th>
                                    <th>Frecuencia7</th>
                                    <th>Frecuencia8</th>
                                    <th>Desinfectante</th>
                                    <th>Encargado</th>
                                    <th>Acción</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mant as $val)
                                <tr>
                                    @php  $fecha = substr($val->created_at,0,10); $frecuencia1 = substr($val->frecuencia1,10,20); $frecuencia2 = substr($val->frecuencia2,10,20); $frecuencia3 = substr($val->frecuencia3,10,20); $frecuencia4 = substr($val->frecuencia4,10,20); $frecuencia5 = substr($val->frecuencia5,10,20); $frecuencia6 = substr($val->frecuencia6,10,20); $frecuencia7 = substr($val->frecuencia7,10,20); $frecuencia8 = substr($val->frecuencia8,10,20); @endphp
                                    <td>{{$fecha}}</td> 
                                    <td>@if(is_null($val->horario)) @else{{$val->horario->nombre_sala}} @endif</td>
                                    <td>@if(($val->frecuencia6)!= '') COMPLETADA <br>  @if(($val->observaciones)!='') {{$val->observaciones}} @endif  @else Incompleta @endif</td>
                                    <td>{{$frecuencia1}}</td>
                                    <td>{{$frecuencia2}}</td>
                                    <td>{{$frecuencia3}}</td>
                                    <td>{{$frecuencia4}}</td>
                                    <td>{{$frecuencia5}}</td>
                                    <td>{{$frecuencia6}}</td>
                                    <td>{{$frecuencia7}}</td>
                                    <td>{{$frecuencia8}}</td>
                                    <td>{{$val->desinfectante}}</td>
                                    <td>{{$val->encargado->nombre1}} {{$val->encargado->nombre2}} {{$val->encargado->apellido1}} {{$val->encargado->apellido2}}</td>
                                    <td id="buton{{$val->id}}">@if(empty($val->frecuencia8))<a onclick="agreagar(<?= $val->id ?>)" id="agregar{{$val->id}}" class="btn btn-primary" type="button">Agregar</a>@endif @if(empty($val->observaciones) && !empty($val->frecuencia8))<a id="agregarobs{{$val->id}}" name="agregarobs" class="btn btn-danger" href="{{route("mantenimientohorario.modalobs",['id'=>$val->id])}}" data-toggle="modal" data-target="#obsefinal">Observaciones</a>@elseif(!empty($val->frecuencia8))<button type="button" class="btn btn-primary">Completa</button> @endif</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-5">
                        <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">Mostrando {{1 + (($mant->currentPage() - 1) * $mant->perPage())}} / {{count($mant) + (($mant->currentPage() - 1) * $mant->perPage())}} de {{$mant->total()}} registros
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                            {{$mant->appends(Request::only(['id','nombre']))->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script src="https://cdn.datatables.net/buttons/1.7.0/js/dataTables.buttons.min.js"></script>

<script>
    function excel() {
        var fecha = $("#fecha").val();
        $("#fechita").val(fecha);
        $("#excii").submit();

    }

    function agreagar(id) {
        $.ajax({
            url: "{{route('mantenimientohorario.agregarhor')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: {
                'id': id,
            },
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                if (data == 'ok') {
                    location.reload();
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
            },
        });
    }
</script>
@endsection