@extends('control_temperatura.control_nevera.base')
@section('action-content')
    @php
        $t = date('Y-m-d');
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
    <section class="content">
        <div class="box">
            <div class="box-header">
                <div class="col-md-12">
                    <div class="col-md-10">
                        <h4 style="text-align: left;">{{ trans('tecnicof.cleaningrecords') }}: <span style="font-weight:bold">
                                {{ $controlarea->nombre }} 
                        </span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <form id="form_fecha" method="POST" action="{{ route('control_temperatura.buscador') }}">
                                {{ csrf_field() }}
                                <input type="hidden" value="{{ $controlarea->id }}" name="id_lugar">
                                <input type="hidden" value="{{$empresa->id}}" name="id_empresa">
                                <input type="hidden" value="{{$id_control_temperatura}}" name="id_control_temperatura" >
                                <div class="form-group  col-xs-4">
                                    <label for="fecha"
                                        class="col-md-6 control-label">{{ trans('tecnicof.from') }}</label>
                                    <div class="col-md-6">
                                        <input style="text-align: center;line-height:10px;" type="date" name="desde"
                                            id="desde" class="form-control" value="{{ $fecha_desde }}">
                                    </div>
                                </div>
                                <div class="form-group  col-xs-4">
                                    <label for="fecha" class="col-md-6 control-label">{{ trans('tecnicof.to') }}</label>
                                    <div class="col-md-6">
                                        <input style="text-align: center;line-height:10px;" type="date" name="hasta"
                                            id="hasta" class="form-control" value="{{ $fecha_hasta }}">
                                    </div>
                                </div>
                                <div class="form-group col-md-1 col-xs-1">
                                    <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                </div>
                            </form>
                            <div class="form-group col-md-1 col-xs-1">
                                <a href="{{ route('control_temperatura.create', ['id' => $controlarea->id, 'tipo' => $controlarea->id_contemperatura,'empresa'=>$empresa->id]) }}"
                                    class="btn btn-primary"><i aria-hidden="true"></i>
                                    {{ trans('tecnicof.register') }}</a>
                            </div>

                            <div class="form-group col-md-1 col-xs-1">

                                <form action="{{ route('reporte_excel.temp') }}" method="POST">
                                    {{ csrf_field() }}
                                    <input type="hidden" value="{{ $id_lugar }}" name="id_lugar">
                                    <input type="hidden" value="{{ $id_control_temperatura }}"
                                        name="id_control_temperatura">
                                    <input type="hidden" value="{{ $empresa->id }}" name="empresa">
                                    <button type="submit" class="btn btn-danger">Reporte</button>
                                </form>
                            </div>




                            <!--
                                    @if ($rolUsuario == 1 || $rolUsuario == 11 || $id_auth == '1111111115')
    <div class="form-group col-md-1 col-xs-1">
                                            <form method="GET" action="{{ route('limpieza_banos.nuevo_reporte') }}">
                                                <input type="hidden" value="{{ $fecha_desde }}" name="fecha_desde">
                                                <input type="hidden" value="{{ $fecha_hasta }}" name="fecha_hasta">
                                                <input type="hidden" value="{{ $controlarea->id }}" name="id_lugar">
                                                <button type="submit" class="btn btn-success"><i aria-hidden="true"></i>
                                                    {{ trans('tecnicof.reports') }}</button>
                                            </form>
                                        </div>
    @endif
                                    -->

                        </div>
                        
                    </div>
                </div>
                <div id="example2_wrapper" class="dataTables_wrapper form-inline dt-bootstrap">
                    <div class="row" id="listado">
                        <div class="table-responsive col-md-12">
                            <table id="example2" class="table table-bordered table-hover dataTable" role="grid"
                                aria-describedby="example2_info" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Temperatura 1</th>
                                        <th>Temperatura 2</th>
                                        <th>Primer Horario</th>
                                        <th>Segundo Horario</th>
                                        <th>Observación</th>
                                        <th>Nevera</th>
                                        <th>Acción</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($registro_nevera as $val)
                                        <tr>
                                            <td>
                                                {{ date('Y-m-d', strtotime($val->created_at)) }}
                                            </td>
                                            <td>
                                                {{ $val->temperatura_uno->temperatura }}°
                                            </td>
                                            <td>
                                                @if (isset($val->temperatura_dos))
                                                    {{ $val->temperatura_dos->temperatura }}
                                                @else
                                                    <select name="temperatura_se" id='temperatura_se{{ $val->id }}'
                                                        class="form-control">
                                                        <option value="">Seleccione</option>
                                                        @foreach ($temperatura_neveras as $temp)
                                                            <option value="{{ $temp->id }}">{{ $temp->temperatura }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                @endif
                                            </td>
                                            <td>
                                                <input readonly type="time" class="form-control"
                                                    value='<?php echo date('H:i', strtotime($val->primer_horario)); ?>' name="segundo_h" />
                                            </td>
                                            <td>
                                                @if (!isset($val->segundo_horario))
                                                    <input readonly type="time" class="form-control"
                                                        value='<?php echo date('H:i'); ?>' name="segundo_h"
                                                        id='segundo_h{{ $val->id }}' />
                                                @else
                                                    {{ $val->segundo_horario }}
                                                @endif

                                            </td>
                                            <td>
                                                {{ $val->observacion }}
                                            </td>
                                            <td>
                                                @if (!is_null($val->id_nevera))
                                                    {{ $val->nombre_nevera->nombre }}
                                                @endif
                                            </td>
                                            <td>
                                                @if ($val->check_s == 0)
                                                    <button class="btn btn-danger" type="button"
                                                        onclick="guardar_registro({{ $val->id }})">
                                                        Guardar
                                                    </button>
                                                @else
                                                    <span class="btn btn-success">
                                                        Completado
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>



                            <div class="row">
                                <div class="col-sm-5">
                                    <div class="dataTables_info" id="example2_info" role="status" aria-live="polite">
                                        {{ trans('tecnicof.showing') }} 1 / {{ count($registro_nevera) }}
                                        {{ trans('limpieza_equipof.de') }} {{ $registro_nevera->total() }}
                                        {{ trans('tecnicof.register') }}
                                    </div>
                                </div>
                                <div class="col-sm-7">
                                    <div class="dataTables_paginate paging_simple_numbers" id="example2_paginate">
                                        {{ $registro_nevera->appends(Request::all())->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

    </section>

    <script>
        function guardar_registro(id) {
            $.ajax({
                url: "{{ route('control_temperatura.update') }}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: {
                    'id': id,
                    'temperatura_se': $(`#temperatura_se${id}`).val(),
                    'segundo_h': $(`#segundo_h${id}`).val(),
                },
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    if (data.status) {
                        location.reload();
                    } else {
                        alert(data.msj);
                    }
                },
                error: function(xhr, status) {
                    alert(`{{ trans('limpieza_equipof.existioproblema') }}`);
                },
            });
        }

        var elementos = document.getElementsByClassName("foto");
        for (var i = 0; i < elementos.length; i++) {
            elementos[i].addEventListener('click', modalfoto);
        }
    </script>
@endsection
