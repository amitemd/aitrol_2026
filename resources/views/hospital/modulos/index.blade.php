@extends('layouts.app-template-h')
@section('content')
<div class="content">
    <section class="content-header">
        <div class="row">
            <div class="col-md-9 col-sm-9">
                <h3>
                    MASTER
                </h3>
            </div>

        </div>
    </section>

    <div class="card card-primary">

        <div class="card-header with-border">
            <h3 class="card-title">{{ trans('hospitalizacion.Pacientes_en') }} {{ $ho_estado_paso->nombre_paso }}</h3>
            <div class="card-tools pull-right">
                <button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
            </div>
        </div>


        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <form id="form_buscar" method="POST" action="{{ route('hospital.index_modulos',['paso' => $ho_estado_paso->id ]) }}">
                        {{ csrf_field() }}
                        <input type="hidden" name="id_estado_paso" id="id_estado_paso" value="{{ $ho_estado_paso->id }}">
                        <div class="form-row">
                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-3 control-label">{{ trans('hospitalizacion.Cedula') }}</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control input-sm" name="cedula" id="cedula" autocomplete="off" value="">
                                </div>
                            </div>
                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-3 control-label">{{ trans('hospitalizacion.Pacientes') }}</label>

                                <input type="text" class="form-control input-sm" name="paciente" id="paciente" autocomplete="off" value="">

                            </div>
                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-4 control-label">{{ trans('hospitalizacion.FechaDesde') }}</label>
                                <div class="col-md-9">
                                    <input type="text" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_desde" id="fecha_desde" autocomplete="off" value="{{ $desde }}">
                                </div>
                            </div>
                            <div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-4 control-label">{{ trans('hospitalizacion.FechaHasta') }}</label>
                                <div class="col-md-9">
                                    <input type="text" data-input="true" class="form-control input-sm flatpickr-basic active" name="fecha_hasta" id="fecha_hasta" autocomplete="off" value="{{ $hasta }}">
                                </div>
                            </div>

                            <!--div class="form-group col-md-3" style="padding-left: 0px;padding-right: 0px;">
                                <label class="col-md-3 control-label">{{ trans('hospitalizacion.Salas') }}</label>
                                <div class="col-md-9">
                                    <select class="form-control" id="sala" name="sala">
                                        <option value=" ">{{ trans('hospitalizacion.Seleccione...') }}</option>
                                        @foreach ($salas as $s)
                                        <option value="{{ $s->id }}">{{ $s->nombre_sala }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div-->

                            <div class="form-group col-md-2">
                                <button class="btn btn-info" type="submit" ><i class="fa fa-search"></i></button>
                            </div>
                            <div class="form-group col-md-2">
                                <a class="btn btn-info" type="button" href="{{ route('hospital.admision', ['ho_estado_paso->id' => $ho_estado_paso->id]) }}"><i class="fa fa-file"></i> {{trans('hospitalizacion.Ingreso')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
                @php
                $paso = Sis_medico\HoEstadoPaso::find($ho_estado_paso->id);
                $id_paso = $paso->id;
                @endphp
                <div class="card-body table-responsive" id="tablaajax">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>{{ trans('hospitalizacion.Fecha') }}</th>
                                <th>{{ trans('hospitalizacion.Ingreso') }}</th>
                                <th>{{ trans('hospitalizacion.Cedula') }}</th>
                                <th>{{ trans('hospitalizacion.Paciente') }}</th>
                                <th>@if($id_paso == 4 or $id_paso == 5) {{ trans('hospitalizacion.Doctor') }} @else {{ trans('hospitalizacion.Usuario') }} @endif</th>
                                
                                @if($id_paso == 2)
                                <th>{{ trans('emergencia.Prioridad') }}</th>
                                @endif
                                <th>{{ trans('hospitalizacion.Accion') }}</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach ($solicitudes as $solicitud)
                            @php
                            $triaje = Sis_medico\Ho_Triaje_Manchester::where('id_ho_solicitud', $solicitud->id)->first();
                            $logSol = $solicitud->log;
                            @endphp
                            <tr>
                                <td>{{ substr($solicitud->agenda->fechaini, 0, 10) }}</td>
                                <td>{{ substr($solicitud->fecha_ingreso, 0, 10) }}</td>
                                <td>{{ $solicitud->agenda->id_paciente }}</td>
                                <td>{{ $solicitud->agenda->paciente->apellido1 }} {{ $solicitud->agenda->paciente->apellido2 }} {{ $solicitud->agenda->paciente->nombre1 }} {{ $solicitud->agenda->paciente->nombre2 }}</td>
                                <td>@if($id_paso == 4 or $id_paso == 5) Dr. @endif {{ $solicitud->agenda->doctor1->apellido1 }}
                                    {{ $solicitud->agenda->doctor1->nombre1 }}
                                </td>
                                
                                @if($id_paso == 2)
                                <td @if(!is_null($triaje)) @if($triaje->prioridad == 5) style="background-color: #1F57A9; color: white" @elseif($triaje->prioridad == 4) style="background-color: #0DD119; color: white" @elseif($triaje->prioridad == 3) style="background-color: #FFE400; color: white" @elseif($triaje->prioridad == 2) style="background-color: #FF6C00; color: white" @elseif($triaje->prioridad == 1) style="background-color: #FF0800; color: white" @endif >
                                    @if(isset($triaje->ho_prioridad))
                                    {{$triaje->ho_prioridad->nombre}}
                                    @endif
                                    @endif
                                </td>
                                @endif
                                <td>

                                    @if (!is_null($paso->ruta))
                                        @if($id_paso == 2)
                                            @if(!is_null($triaje))
                                            <a href="{{route('hospital.resultadomanchester',$triaje->id)}}" class="btn btn-sm btn-success"><i class="far fa-file-alt"></i>{{trans('emergencia.DatosTriaje')}}</a>
                                            @endif
                                            {{-- <a href="{{route('hospital.formulario008_pdf', ['id_paciente' => $solicitud->id])}}" target="_blank"class="btn btn-info btn-xs" ><i class="fa fa-download"></i> {{trans('emergencia.Formulario008')}}</a> --}}
                                            <a href="{{route('hospital.nuevo.formulario008_pdf', ['id_paciente' => $solicitud->id])}}" target="_blank"class="btn btn-info btn-xs" ><i class="fa fa-download"></i> {{trans('emergencia.Formulario008')}}</a>
                                        @endif
                                        @if ($id_paso == 4)
                                            <a href="{{ route($paso->ruta, ['tipo' => 0, 'id_solicitud' => $solicitud->id]) }}" class="btn btn-info" type="button"><i class="fa fa-file"></i> {{trans('hospitalizacion.VerDetalle')}}</a>
                                        @elseif($id_paso == 5)
                                            <a href="{{ route($paso->ruta, ['tipo' => 1, 'id_solicitud' => $solicitud->id]) }}" class="btn btn-info" type="button"><i class="fa fa-file"></i> {{trans('hospitalizacion.VerDetalle')}}</a>
                                        
                                        @else
                                        <a href="{{ route($paso->ruta, ['id_solicitud' => $solicitud->id]) }}" class="btn btn-info" type="button"><i class="fa fa-file"></i>{{trans('hospitalizacion.VerDetalle')}}</a>
                                            @if($id_paso == 3)
                                                <a href="{{route('habitacion.formulario005_pdf', ['id_solicitud' => $solicitud->id])}}" target="_blank"class="btn btn-info btn-xs" ><i class="fa fa-download"></i>{{trans('emergencia.Formualrio005')}}</a>
                                            @endif
                                        @endif
                                    @endif
                                    <a href="{{route('formulario.001',['id_solicitud' => $solicitud->id])}}" target="_blank" class="btn btn-info btn-xs"><i class="far fa-download"></i> {{trans('emergencia.formulario')}} 001</a>
                                    <button type="button" class="btn btn-info btn-xs" onclick="cargar_planilla('{{$solicitud->id}}')"><i class="far fa-download"></i> {{trans('hospitalizacion.planillar')}}</button>
                                   
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function buscador() {
        $.ajax({
            url: "{{ route('buscador.ajax_emergencia') }}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#form_buscar').serialize(),
            type: 'GET',
            dataType: 'html',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();
                $("#tablaajax").empty().html(datahtml).fadeIn();
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });
    }

    function cargar_planilla(id) {
        $.ajax({
            url: "{{ url('planillar_hospital') }}/"+id,
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#form_buscar').serialize(),
            type: 'GET',
            dataType: 'json',
            beforeSend: function() {

                //$('#loading').show();
            },
            success: function(datahtml, data) {
                //$('#loading').hide();planillar_hospital/{id}
                //console.log(datahtml.msj,data);
                location.href = "{{ url('planillar_hospital/detalle') }}/"+id;
            },
            error: function(xhr, status) {
                alert('Existió un problema en la Tabla');

            },
        });
    }
</script>
@endsection