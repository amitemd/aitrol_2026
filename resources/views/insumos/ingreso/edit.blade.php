@extends('insumos.producto.base')

@section('action-content')
<div class="container">
    <div class="row">
        <!--left-->
            <div class="box box-primary col-xs-24"> 
                <div class="box-header with-border"><h3 class="box-title">Editar producto</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('producto.update', ['id' => $producto->id]) }}">
                    <div class="box-body col-xs-24">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ $producto->id }}">

                        <!--Razón Social-->
                        <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                            <label for="codigo" class="col-md-4 control-label">Codigo</label>
                            <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" name="codigo" value="{{ $producto->codigo }}" required autofocus>
                                @if ($errors->has('codigo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('codigo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>       
                        <!--RUC-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-7">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $producto->nombre }}" required autofocus>
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                            <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $producto->descripcion }}" required autofocus>
                                @if ($errors->has('descripcion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('medida') ? ' has-error' : '' }}">
                            <label for="medida" class="col-md-4 control-label">Medida</label>
                            <div class="col-md-7">
                                <select id="medida" name="medida" class="form-control" required="required">
                                    <option {{ $producto->medida == 'Uni' ? 'selected' : ''}} value="Uni">Unidad</option>
                                    <option {{ $producto->medida == 'Kg' ? 'selected' : ''}} value="Kg">Kilogramos</option>
                                    <option {{ $producto->medida == 'G' ? 'selected' : ''}} value="G">Gramos</option>
                                    <option {{ $producto->medida == 'Mg' ? 'selected' : ''}} value="Mg">Miligramos</option>
                                    <option {{ $producto->medida == 'Lb' ? 'selected' : ''}} value="Lb">Libras</option>
                                </select> 
                                @if ($errors->has('medida'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('medida') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('despacho') ? ' has-error' : '' }}">
                            <label for="despacho" class="col-md-4 control-label">Forma de Despacho</label>
                            <div class="col-md-7">
                                <select id="despacho" name="despacho" class="form-control" required="required">
                                    <option {{ $producto->despacho == 1 ? 'selected' : ''}}  value="1">codigo de serie</option>
                                    <option {{ $producto->despacho == 0 ? 'selected' : ''}} value="0">codigo de producto</option>
                                </select> 
                                @if ($errors->has('despacho'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('despacho') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        <!--eminimomail-->
                        <div class="form-group col-xs-6{{ $errors->has('minimo') ? ' has-error' : '' }}">
                            <label for="minimo" class="col-md-4 control-label">Stock Minimo</label>
                            <div class="col-md-7">
                            <input id="minimo" type="text" class="form-control" name="minimo" value="{{ $producto->minimo }}" required>
                                @if ($errors->has('minimo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('minimo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Precio de Compra-->
                        <div class="form-group col-xs-6{{ $errors->has('precio_compra') ? ' has-error' : '' }}">
                            <label for="precio_compra" class="col-md-4 control-label">Precio de Compra</label>
                            <div class="col-md-7">
                            <input id="precio_compra" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio_compra" value="{{ $producto->precio_compra }}" required>
                                @if ($errors->has('precio_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('precio_compra') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <!--Precio de Venta-->
                        <div class="form-group col-xs-6{{ $errors->has('precio_venta') ? ' has-error' : '' }}">
                            <label for="precio_venta" class="col-md-4 control-label">Precio de Venta</label>
                            <div class="col-md-7">
                            <input id="precio_venta" type="text" class="form-control" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" name="precio_venta" value="{{ $producto->precio_venta }}" required>
                                @if ($errors->has('precio_venta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('precio_venta') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                            <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>
                            <div class="col-md-7">
                            <select id="id_proveedor" name="id_proveedor" class="form-control">
                                @foreach($proveedores as $value)
                                        @if ($value->estado != 0)
                                                <option {{ $producto->id_proveedor == $value->id ? 'selected' : ''}} value="{{$value->id}}">{{$value->nombrecomercial}}</option>
                                        @endif    
                                    @endforeach
                                        
                                </select>  
                                @if ($errors->has('id_proveedor'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id_proveedor') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                Actualizar
                                </button>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>  
    </div>
</div>    

@endsection
