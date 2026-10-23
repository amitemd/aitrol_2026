@extends('servicios_generales.limpieza_area.base')
@section('action-content')
@php
$fecha_desde = date('Y-m-d');
$fecha_hasta = date('Y-m-d');
$rolUsuario = Auth::user()->id_tipo_usuario;
$id_auth = Auth::user()->id;
@endphp
<style type="text/css">
    th,
    td {
        text-align: center;
    }

    .imgf {
        cursor: pointer;
    }
</style>
<div class="modal fade" id="editar" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 80%;">
        </div>
    </div>
</div>
<div class="modal fade" id="foto" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 80%;">
        </div>
    </div>
</div>

<div id="fotito">
</div>
<div class="modal fade" id="foto2" data-keyboard="false" tabindex=null role="dialog" aria-labelledby="myModalDoctor" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="width: 80%;">
        </div>
    </div>
</div>

<section class="content">
    <div class="box">
        <div class="box-header">
            <div class="col-md-12">
                <div class="col-md-10">
                    <h4 style="text-align: left;">REGISTROS DE LIMPIEZA DE AREA</h4>
                </div>
            </div>
        </div>
        <div class="box-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <form id="form_fecha" method="POST" action="{{route('buscador_limpieza_area')}}">
                            {{ csrf_field() }}
                            <div class="form-group  col-xs-4">
                                <label for="fecha" class="col-md-6 control-label">Fecha Desde</label>
                                <div class="col-md-6">
                                    <input style="text-align: center;line-height:10px;" type="date" name="desde" id="desde" class="form-control" value="{{$fecha_desde}}">
                                </div>
                            </div>
                            <div class="form-group  col-xs-4">
                                <label for="fecha" class="col-md-6 control-label">Fecha Hasta</label>
                                <div class="col-md-6">
                                    <input style="text-align: center;line-height:10px;" type="date" name="hasta" id="hasta" class="form-control" value="{{$fecha_hasta}}">
                                </div>
                            </div>
                            <div class="form-group col-md-1 col-xs-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                            </div>
                        </form>
                            @php
                            $permisos = \Sis_medico\Agenda_Permiso::where('id_usuario', $id_auth)->where('estado', 3)->first();
                            @endphp
                        @if($rolUsuario == 1 || $rolUsuario == 11 || $id_auth=='1111111115'|| !is_null($permisos) )
                        <div class="form-group col-md-1 col-xs-1">
                            <form method="GET" action="{{route('limpieza_area.reporte_area')}}">
                                <input type="hidden" value="{{$fecha_desde}}" name="fecha_desde">
                                <input type="hidden" value="{{$fecha_hasta}}" name="fecha_hasta">
                                {{--<!--input type="hidden" value="{{$sala->id}}" name="id_sala"-->--}}
                                <button type="submit" class="btn btn-success"><i aria-hidden="true"></i> Reportes</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                <div class="row" id="listado">
                    <div class="table-responsive col-md-12">
                        <table id="example2" class="table table-bordered table-hover dataTable" role="grid" aria-describedby="example2_info" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora evidencia antes</th>
                                    <th>Hora evidencia después</th>
                                    <th>Responsable</th>
                                    <th>Evidencia antes</th>
                                    <th>Evidencia después</th>
                                    <th>Observaciones</th>
                                    <th>Accion</th>
                                </tr>
                            </thead>

                            @foreach($limpieza as $value )

                            @php
                            $fecha = substr($value->created_at, 0, 11);
                            $frecuencia1 = substr($value->created_at, 11, 20);
                            $emplace=asset('/');
                            $remplace= str_replace('public','storage',$emplace);
                            $created_at = substr($value->created_at, 11, 20);
                            $updated_at = substr($value->updated_at, 11, 20);

                            @endphp
                            <tr>
                                <td>{{$fecha}}</td>
                                <td>{{$frecuencia1}}</td>
                                <td>@if(!is_null($value->path_despues)){{$updated_at}}@endif</td>
                                <td>@if(!is_null($value->id_usuariocrea) || !empty($value->path_despues)){{$value->encargado->nombre1}} {{$value->encargado->nombre2}} {{$value->encargado->apellido1}} {{$value->encargado->apellido2}}@endif</td>
                                <td> @if(!is_null($value->path_antes) || !empty($value->path_antes))
                                    <img id="{{$value->id}}" data-name="1" class="imgf foto" src="{{$remplace.'app/avatars/'.$value->path_antes}}" width="50" alt="">@endif

                                </td>
                                <td>@if(!is_null($value->path_despues) && !empty($value->path_despues))
                                    <img id="{{$value->id}}" data-name="2" class="imgf foto" src="{{$remplace.'app/avatars/'.$value->path_despues}}" width="50" alt="">@endif


                                </td>
                                <td>{{$value->observaciones}}</td>
                                <td id="buton{{$value->id}}">
                                    @if(($value->path_antes)!="" && ($value->path_despues)!="")
                                    <button type="button" class="btn btn-success">Completo</button>
                                    @else
                                    <a id="editar" class="btn btn-info btn-xs" href="{{ route('edit_limpieza_area', ['id' => $value->id]) }}"><span>Editar Registro</span></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </table>
                        <div class="row">
                            <div class="col-sm-5">
                                <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">{{trans('contableM.mostrando')}} 1 / {{count($limpieza)}} de {{$limpieza->total()}} {{trans('contableM.registros')}}</div>
                            </div>
                            <div class="col-sm-7">
                                <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                    {{ $limpieza->appends(Request::all())->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

</section>

<script>
    var elementos = document.getElementsByClassName("foto");
    for (var i = 0; i < elementos.length; i++) {
        elementos[i].addEventListener('click', modalfoto);
    }


    function modalfoto() {
        //console.log(this);
        $.ajax({
            type: 'get',
            url: "{{route('modal_foto_limpieza_area')}}",
            data: {
                'id': this.id,
                'tipo': this.dataset.name,
            },
            datatype: 'html',
            success: function(data) {

                $("#fotito").html(data);
                $("#fotito").children().modal();

            },
            error: function(data) {
                // console.log(data);
            }

        });
    }
</script>
<!--
<script text="text/javascript">
  $(document).ready(function() {
    $('#example2').DataTable({
      'paging': true,
      'lengthChange': false,
      'searching': false,
      'responsive': false,
      'ordering': false,
      'info': false,
      'autoWidth': false,
      'sInfoEmpty': false,
      'sInfoFiltered': false,
      'language': {
        "url": "//cdn.datatables.net/plug-ins/1.10.20/i18n/Spanish.json"
      }
    });
  });
</script>
-->
@endsection
