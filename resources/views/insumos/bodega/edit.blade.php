@extends('insumos.bodega.base')
@section('action-content')
<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
    .select2 {
        width: 100% !important;
    }
</style>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24">
            <div class="box box-primary">
                <div class="box-header">
                    <div class="col-md-9">
                        <h3 class="box-title">{{trans('winsumos.actualizar_bodega')}}</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" onclick="goBack()" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('bodega.update', ['id' => $bodega->id]) }}" enctype="multipart/form-data">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre')}}</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $bodega->nombre }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('id_hospital') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="col-md-4 control-label">{{trans('winsumos.hospital')}}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="id_hospital">
                                    @foreach($hospital as $value)
                                    <option value="{{$value->id}}" @if($bodega->id_hospital == $value->id) selected @endif >{{$value->nombre_hospital}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-4 control-label">{{trans('winsumos.estado')}}</label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado">
                                    <option {{$bodega->estado == 0 ? 'selected' : ''}} value="0">{{trans('winsumos.inactivo')}}</option>
                                    <option {{$bodega->estado == 1 ? 'selected' : ''}} value="1">{{trans('winsumos.activo')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">{{trans('winsumos.color_etiqueta')}}</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ $bodega->color }}" required>
                                <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span>
                                @if ($errors->has('color'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('color') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('tipo') ? ' has-error' : '' }}">
                            <label for="tipo" class="col-md-4 control-label"> Tipo </label>
                            <div class="col-md-6">
                                <select class="form-control" name="tipo" id="tipoSelect">
                                    <option @if($bodega->tipo == 0) selected @endif value="0"> Compra </option>
                                    <option @if($bodega->tipo == 1) selected @endif value="1"> Venta </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="ventaSelectDiv" style="display: none;">
                            <label for="venta" class="col-md-4 control-label"> cliente </label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="id_cliente" id="id_cliente">
                                    <option value="">{{trans('mcaja.seleccione')}}...</option>
                                    @foreach($clientes as $value)
                                    <option @if($bodega->id_cliente == $value->identificacion) selected @endif value="{{$value->identificacion}}">{{$value->nombre}}</option>
                                    @endforeach

                                </select>
                            </div>
                        </div>

                        
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{trans('winsumos.actualizar')}}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

@endsection
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        if ($("#tipoSelect").val() === "1") {
            $("#ventaSelectDiv").show();
        } else {
            $("#ventaSelectDiv").hide();
        }
        $("#tipoSelect").change(function() {
            if ($(this).val() === "1") {
                $("#ventaSelectDiv").show();
            } else {
                $("#ventaSelectDiv").hide();
            }
        });
        $('.select2').select2({
            tags: false,
            allowClear: true,
            placeholder: "--Seleccionar--",
            minimumInputLength: 3,
            ajax: {
                url: "{{url('/')}}/ventas/ventas?opcion=getCliente",
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function(params) {
                    return {
                        term: params.term
                    };
                },
                processResults: function(response) {
                    return {
                        results: response.aaData
                    };
                },
                cache: true,
                dropdownCssClass: "bigdrop"
            }
        });
    });
</script>
<script type="text/javascript">
    function goBack() {
        window.history.back()
    }
</script>

