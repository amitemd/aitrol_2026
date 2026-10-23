@extends('control_temperatura.control_nevera.base')
@section('action-content')
    @php
        $t = date('Y-m-d');
        $rolUsuario = Auth::user()->id_tipo_usuario;
        $id_auth = Auth::user()->id;
        $fecha_desde = date('Y-m-d');
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
                    <h4 style="text-align: left;">REPORTE</span>
                    </h4>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <form id="form_fecha" method="get" action="{{ route('excel_buscador.temp') }}">
                               
                                <input type="hidden" value="{{ $id_lugar }}" name="id_lugar">
                                <input type="hidden" value="{{ $id_control_temperatura }}" name="id_control_temperatura">
                                <input type="hidden" value="{{$empresa}}" name="id_empresa">
                                <div class="form-group  col-xs-3">
                                    <label for="fecha"
                                        class="col-md-2 control-label">{{ trans('tecnicof.from') }}</label>
                                    <div class="col-md-10">
                                        <input style="text-align: center;line-height:10px;" type="date" name="desde"
                                            id="desde" class="form-control" value="{{ $fecha_desde }}">
                                    </div>
                                </div>
                                <div class="form-group  col-xs-3">
                                    <label for="fecha" class="col-md-2 control-label">{{ trans('tecnicof.to') }}</label>
                                    <div class="col-md-10">
                                        <input style="text-align: center;line-height:10px;" type="date" name="hasta"
                                            id="hasta" class="form-control" value="{{ $fecha_desde }}">
                                    </div>
                                </div>
                                <div class="form-group  col-xs-3">
                                    <label for="fecha" class="col-md-2 control-label">Tipo</label>
                                    <div class="col-md-10">
                                        <select name="tipo" id="tipo" class="form-control">
                                            <option value="">Seleccione</option>
                                            <option value="2">Control Ambiente</option>
                                            <option value="1">Control Nevera</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group  col-xs-2">
                                    <label for="id_nevera" class="col-md-4 control-label">Nevera</label>
                                    <div class="col-md-8">
                                        <select name="id_nevera" id="id_nevera" class="form-control">
                                            <option value="">Seleccione</option>
                                            @foreach ($nevera as $value)
                                                <option value="{{ $value->id }}">{{ $value->nombre }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="form-group col-md-1 col-xs-1">
                                    <button type="submit" class="btn btn-primary btn-sm" id="boton_buscar">
                                        <span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

    </section>


    <script>
        function regresar() {
            history.back();
        }
    </script>
@endsection
