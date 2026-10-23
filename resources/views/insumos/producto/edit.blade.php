@extends('insumos.producto.base')
@section('action-content')

<link rel="stylesheet" href="{{ asset("/css/icheck/all.css")}}">
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
                <h3 class="box-title">{{trans('winsumos.editar_producto')}}</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;{{trans('winsumos.regresar')}}
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" enctype="multipart/form-data" role="form" method="POST" action="{{ route('producto.update', ['id' => $producto->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="id" value="{{ $producto->id }}">
                    <!--Codigo-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">{{trans('winsumos.codigo')}}</label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" name="codigo" value="{{ $producto->codigo }}" disabled required autofocus>
                            @if ($errors->has('codigo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('codigo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Codigo IESS-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo_iess') ? ' has-error' : '' }}">
                        <label for="codigo_iess" class="col-md-4 control-label">{{trans('winsumos.codigo_referencial')}}</label>
                        <div class="col-md-7">
                            <input id="codigo_iess" type="text" class="form-control" name="codigo_iess" value="{{ $producto->codigo_iess }}" autofocus>
                            @if ($errors->has('codigo_iess'))
                            <span class="help-block">
                                <strong>{{ $errors->first('codigo_iess') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Nombre-->
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-4 control-label">{{trans('winsumos.nombre')}}</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $producto->nombre }}" required autofocus>
                            @if ($errors->has('nombre'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Descripcion-->
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">{{trans('winsumos.descripcion')}}</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $producto->descripcion }}" required autofocus>
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Cantidad de medida-->
                    <div class="form-group col-xs-6{{ $errors->has('cantidad_unidad') ? ' has-error' : '' }}">
                        <label for="cantidad_unidad" class="col-md-4 control-label">{{trans('winsumos.cantidad_medida')}}</label>
                        <div class="col-md-7">
                            <input id="cantidad_unidad" type="text" class="form-control" name="cantidad_unidad" value="{{ $producto->cantidad_unidad }}" required autofocus>
                            @if ($errors->has('cantidad_unidad'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cantidad_unidad') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Medida-->
                    <div class="form-group col-xs-6{{ $errors->has('medida') ? ' has-error' : '' }}">
                        <label for="medida" class="col-md-4 control-label">{{trans('winsumos.medida')}}</label>
                        <div class="col-md-7">
                            <select id="medida" name="medida" class="form-control" required="required">
                                <option {{ $producto->medida == 'Uni' ? 'selected' : ''}} value="Uni">{{ trans('winsumos.unidad') }}</option>
                                <option {{ $producto->medida == 'Kg' ? 'selected' : ''}} value="Kg">{{ trans('winsumos.kilogramos') }}</option>
                                <option {{ $producto->medida == 'G' ? 'selected' : ''}} value="G">{{ trans('winsumos.gramos') }}</option>
                                <option {{ $producto->medida == 'Mg' ? 'selected' : ''}} value="Mg">{{ trans('winsumos.miligramos') }}</option>
                                <option {{ $producto->medida == 'Ml' ? 'selected' : ''}} value="Ml">{{ trans('winsumos.mililitros') }}</option>
                                <option {{ $producto->medida == 'L' ? 'selected' : ''}} value="L">{{ trans('winsumos.litros') }}</option>
                                <option {{ $producto->medida == 'Lb' ? 'selected' : ''}} value="Lb">{{ trans('winsumos.libras') }}</option>
                                <option {{ $producto->medida == 'm' ? 'selected' : ''}} value="m">{{ trans('winsumos.metros') }}</option>
                                <option {{ $producto->medida == 'cm' ? 'selected' : ''}} value="cm">{{ trans('winsumos.centimetros') }}</option>
                                <option {{ $producto->medida == 'mm' ? 'selected' : ''}} value="mm">{{ trans('winsumos.milimetros') }}</option>
                            </select>
                            @if ($errors->has('medida'))
                            <span class="help-block">
                                <strong>{{ $errors->first('medida') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Forma de Despacho-->
                    <div class="form-group col-xs-6{{ $errors->has('despacho') ? ' has-error' : '' }}">
                        <label for="despacho" class="col-md-4 control-label">{{trans('winsumos.forma_despacho')}}</label>
                        <div class="col-md-7">
                            <select id="despacho" name="despacho" class="form-control" required="required" onchange="usos_por_kardex(this)">
                                <option {{ $producto->despacho == 1 ? 'selected' : ''}} value="1">{{trans('winsumos.codigo_serie')}}</option>
                                <option {{ $producto->despacho == 0 ? 'selected' : ''}} value="0">{{trans('winsumos.codigo_producto')}}</option>
                            </select>
                            @if ($errors->has('despacho'))
                            <span class="help-block">
                                <strong>{{ $errors->first('despacho') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Stock Minimo-->
                    <div class="form-group col-xs-6{{ $errors->has('minimo') ? ' has-error' : '' }}">
                        <label for="minimo" class="col-md-4 control-label">{{trans('winsumos.stock_minimo')}}</label>
                        <div class="col-md-7">
                            <input id="minimo" onchange="return validarRango(this);" type="number" class="form-control" name="minimo" value="{{ $producto->minimo }}" required>
                            @if ($errors->has('minimo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('minimo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Registro Sanitario-->
                    <div class="form-group col-xs-6{{ $errors->has('registro_sanitario') ? ' has-error' : '' }}">
                        <label for="registro_sanitario" class="col-md-4 control-label">{{trans('winsumos.registro_sanitario')}}</label>
                        <div class="col-md-7">
                            <input id="registro_sanitario" type="registro_sanitario" class="form-control" name="registro_sanitario" value="{{ $producto->registro_sanitario }}" required>
                            @if ($errors->has('registro_sanitario'))
                            <span class="help-block">
                                <strong>{{ $errors->first('registro_sanitario') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('id_marca') ? ' has-error' : '' }}">
                        <label for="id_marca" class="col-md-4 control-label">{{trans('winsumos.marcas')}}</label>
                        <div class="col-md-7">
                            <select id="id_marca" name="id_marca" class="form-control" required="required">
                                <option value="">{{trans('winsumos.seleccione')}}..</option>
                                @foreach($marcas as $marca)
                                <option @if($marca->id==$producto->marca->id) selected @endif value="{{$marca->id}}"> {{$marca->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_marca'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_marca') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Tipo de Producto-->
                    <div class="form-group col-xs-6{{ $errors->has('id_tipo') ? ' has-error' : '' }}">
                        <label for="id_tipo" class="col-md-4 control-label">{{trans('winsumos.categoria')}}</label>
                        <div class="col-md-7">
                            <select id="id_tipo" name="id_tipo" class="form-control" required="required">
                                <option value="">{{trans('winsumos.seleccione')}}..</option>
                                @foreach($tipos as $tipo)
                                <option @if($tipo->id== $producto->tipo_producto ) selected @endif value="{{ $tipo->id }}">{{ $tipo->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('id_tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Cantidad de Usos-->
                    <div class="form-group col-xs-6{{ $errors->has('uso') ? ' has-error' : '' }}">
                        <label for="uso" class="col-md-4 control-label">{{trans('winsumos.cantidad_usos')}}</label>
                        <div class="col-md-7">
                            <input id="uso" onchange="return validarRango_uso(this);" type="number" class="form-control" name="uso" value="{{ $producto->usos }}" required autofocus>
                            @if ($errors->has('uso'))
                            <span class="help-block">
                                <strong>{{ $errors->first('uso') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--Forma de Despacho-->
                    <div class="form-group col-xs-6{{ $errors->has('iva') ? ' has-error' : '' }}">
                        <label for="iva" class="col-md-4 control-label">{{trans('winsumos.posee_iva')}}</label>
                        <div class="col-md-7">
                            <select id="iva" name="iva" class="form-control" required="required">
                                <option {{ $producto->iva == 1 ? 'selected' : ''}} value="1">{{trans('winsumos.si')}}</option>
                                <option {{ $producto->iva == 0 ? 'selected' : ''}} value="0">{{trans('winsumos.no')}}</option>
                            </select>
                            @if ($errors->has('iva'))
                            <span class="help-block">
                                <strong>{{ $errors->first('iva') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <!--estado-->
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-4 control-label">{{trans('winsumos.estado')}}</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control" required="required">
                                <option {{ $producto->estado == 1 ? 'selected' : ''}} value="1">{{trans('winsumos.activo')}}</option>
                                <option {{ $producto->estado == 0 ? 'selected' : ''}} value="0">{{trans('winsumos.inactivo')}}</option>
                            </select>
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                <strong>{{ $errors->first('estado') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-6">
                        <label class="col-md-4 control-label">{{trans('winsumos.usar_mismo_codigo')}}</label>
                        <input class="col-md-7" id="codigo_siempre" name="codigo_siempre" type="checkbox" value="1" class="flat-blue" style="position: absolute; opacity: 0;" {{ $producto->codigo_siempre == '1' ? 'checked' : ''}}>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('cod_general') ? ' has-error' : '' }}">
                        <label for="cod_general" class="col-md-4 control-label">{{trans('winsumos.codigo_general')}}</label>
                        <div class="col-md-7">
                            <select class="form-control select2" name="cod_general" id="cod_general" style="width: 100%;">
                                @php $equipos= DB::table('ct_productos_insumos')->where('id_insumo',$producto->id)->first(); @endphp
                                @if(!is_null($equipos))
                                @php
                                $producto_v = DB::table('ct_productos')->find($equipos->id_producto);
                                @endphp
                                <option value="{{$equipos->id_producto}}">{{$producto_v->codigo}} | {{$producto_v->nombre}}</option>
                                @endif

                            </select>
                        </div>
                    </div>

                    <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="tipo" class="col-md-4 control-label">{{trans('winsumos.Tipo')}}</label>
                        <div class="col-md-7">
                            <select id="tipo" name="tipo" class="form-control" required>
                                <option value="0">{{trans('winsumos.otros')}}</option>
                                @foreach($tipo_plantilla as $tp)
                                <option @if($producto->tipo == $tp->id) selected @endif value="{{$tp->id}}">{{$tp->nombre}}</option>
                                @endforeach
                            </select>
                            @if ($errors->has('tipo'))
                            <span class="help-block">
                                <strong>{{ $errors->first('tipo') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    @if($empresa->imagen_producto == 1)
                    <div class="form-group col-xs-6{{ $errors->has('imagen_producto') ? ' has-error' : '' }}">
                        <label for="imagen_producto" class="col-md-4 control-label">{{trans('winsumos.Foto')}}</label>
                        <div class="col-md-7">
                            <input id="imagen_producto" type="file" class="form-control" name="imagen_producto" value="{{$producto->imagen_producto}}">
                            @if ($errors->has('imagen_producto'))
                            <span class="help-block">
                                <strong>{{ $errors->first('imagen_producto') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <!--Usos Kardex VICTOR-->
                    <div class="form-group col-xs-6">
                        <label class="col-md-4 control-label">{{trans('winsumos.kardex_usos')}}</label>
                        <input class="col-md-7" id="kardex_uso" name="kardex_uso" type="checkbox" value="1" {{ $producto->kardex_uso == '1' ? 'checked' : ''}} disabled>
                    </div>
                    <div class="form-group col-xs-6{{ $errors->has('cantidad_usos_kardex') ? ' has-error' : '' }}">
                        <label for="cantidad_usos_kardex" class="col-md-4 control-label">{{trans('winsumos.cantidad_usos_kardex')}}</label>
                        <div class="col-md-7">
                            <input id="cantidad_usos_kardex" type="number" class="form-control" name="cantidad_usos_kardex" value="{{ $producto->cantidad_usos_kardex }}" autofocus disabled>
                            @if ($errors->has('cantidad_usos_kardex'))
                            <span class="help-block">
                                <strong>{{ $errors->first('cantidad_usos_kardex') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    <!--Usos Kardex VICTOR-->
                    <div class="clearfix">
                        <span></span>
                    </div>
                    <br><br>
                    <!--Precios-->

                    <!--Button Actualizar-->
                    <div class="form-group col-xs-10" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{trans('winsumos.actualizar')}}
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{ asset ("/js/icheck.js") }}"></script>
    <script>
        $(document).ready(function() {
            $('#codigo_siempre').iCheck({
                checkboxClass: 'icheckbox_flat-blue',
                increaseArea: '20%' // optional
            });
            usos_por_kardex(document.getElementById('despacho'));
        });
    </script>

    <script type="text/javascript">
        var fila = '<td><input class="form-control input-sm" type="text" style="width: 80%;height:20px;" placeholder="#" name="nivel[]" required>' +
            '</td><td><input class="form-control input-sm" type="text" style="width: 80%;height:20px;" onkeypress="return isNumberKey(event)" placeholder="0.00" onblur="this.value=parseFloat(this.value).toFixed(2);" name="precio[]" required>' +
            '</td><td><button type="button" class="btn btn-danger btn-gray delete"><i class="glyphicon glyphicon-trash" aria-hidden="true"></i>' +
            '</button></td>';

        function validarRango(elemento) {
            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1) {
                alert("La cantidad debe ser mayor o igual a 1");
                $('#minimo').val("1");
                return false;
            }
            return true;
        }


        function validarRango_uso(elemento) {
            var numero = parseInt(elemento.value, 10);
            //Validamos que se cumpla el rango
            if (numero < 1) {
                alert("La cantidad debe ser mayor o igual a 1");
                $('#uso').val("1");
                return false;
            }
            return true;
        }

        function goBack() {
            window.history.back();
        }
        $('.select2').select2({
            tags: false
        });

        $('body').on('click', '.delete', function() {
            $(this).parent().parent().remove();
        });

        function usos_por_kardex(item) {
            if (item.value == '1') {
                $('#kardex_uso').removeAttr("disabled");
                $('#cantidad_usos_kardex').removeAttr("disabled");
            } else {
                $('#kardex_uso').attr("disabled", "disabled");
                $('#cantidad_usos_kardex').attr("disabled", "disabled");
            }
            console.log(item);
        }

        function nuevo() {
            var nuevafila = fila;
            var rowk = document.getElementById("agregar_cuentas").insertRow(-1);
            rowk.innerHTML = fila;
            //rowk.className="well";


        }
        $('#cod_general').select2({
            placeholder: "Seleccione un producto...",
            allowClear: true,
            cache: true,
            ajax: {
                url: '{{route("importaciones.productos")}}',
                data: function(params) {
                    var query = {
                        search: params.term,
                        type: 'public'
                    }
                    return query;
                },
                processResults: function(data) {
                    return {
                        results: data
                    };
                }
            }
        });
    </script>

</section>
@endsection