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
                        <h3 class="box-title">{{trans('winsumos.agregar_nueva_bodega')}}</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" onclick="goBack()" class="btn btn-primary btn-sm">
                            <span class="glyphicon glyphicon-arrow-left">{{trans('winsumos.regresar')}}</span>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('bodega.store') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre_bodega')}}</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" required autofocus style="text-transform:uppercase;">
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('id_hospital') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="col-md-4 control-label">{{trans('winsumos.hospital')}}</label>
                            <div class="col-md-6">
                                <select id="id_hospital" name="id_hospital" class="form-control" required>
                                    <option value="">{{trans('winsumos.seleccione')}}</option>
                                    @foreach ($hospital as $hospital)
                                    <option @if(old('id_hospital')==$hospital->id){{"selected"}}@endif value="{{$hospital->id}}">{{$hospital->nombre_hospital}} </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('id_hospital'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id_hospital') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">{{trans('winsumos.color_etiqueta')}}</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="@if(!is_null(old('color'))) {{ old('color') }} @else #000000 @endif" required>
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
                                    <option value="0"> Compra </option>
                                    <option value="1"> Venta </option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group" id="ventaSelectDiv" style="display: none; z-index: 9999; height: auto;">
                            <label for="venta" class="col-md-4 control-label"> cliente </label>
                            <div class="col-md-6">
                                <select class="form-control select2" name="id_cliente" id="id_cliente" style="position: sticky;">
                                    <option value="">{{trans('mcaja.seleccione')}}...</option>
                                </select>
                            </div>

                        </div>
                        <br>


                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    {{trans('winsumos.guardar')}}
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