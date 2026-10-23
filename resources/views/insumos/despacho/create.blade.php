@extends('insumos.producto.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nuevo Producto</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('producto.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--RUC-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Codigo</label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" style="text-transform:uppercase;"  maxlength="13" required autofocus onkeyup="validarCedula(this.value);">
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!--Nombre-->
                        <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre </label>
                            <div class="col-md-7">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
               
                    <!--Descripcion-->
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-4 control-label">Descripcion</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
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
                                    <option value="">Seleccione..</option>
                                    <option value="Uni">Unidad</option>
                                    <option value="Kg">Kilogramos</option>
                                    <option value="G">Gramos</option>
                                    <option value="Mg">Miligramos</option>
                                    <option value="Lb">Libras</option>
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
                                    <option value="">Seleccione..</option>
                                    <option value="1">codigo de serie</option>
                                    <option value="0">codigo de producto</option>
                                </select> 
                                @if ($errors->has('despacho'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('despacho') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--Direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('minimo') ? ' has-error' : '' }}">
                            <label for="minimo" class="col-md-4 control-label" >Stock Minimo</label>

                            <div class="col-md-7">
                                <input id="minimo" type="text" class="form-control" name="minimo" value="{{ old('minimo') }}" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" required autofocus>

                                @if ($errors->has('minimo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('minimo') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--precio_compra-->                        
                        <div class="form-group col-xs-6{{ $errors->has('precio_compra') ? ' has-error' : '' }}">
                            <label for="precio_compra" class="col-md-4 control-label">Precio de Compra</label>

                            <div class="col-md-7">
                                <input id="precio_compra" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;" type="precio_compra" class="form-control" name="precio_compra" value="{{ old('precio_compra') }}" required>

                                @if ($errors->has('precio_compra'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('precio_compra') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--precio_venta-->
                        <div class="form-group col-xs-6{{ $errors->has('precio_venta') ? ' has-error' : '' }}">
                            <label for="precio_venta" class="col-md-4 control-label">Precio de Venta</label>

                            <div class="col-md-7">
                                <input id="precio_venta" onKeypress="if (event.keyCode < 45 || event.keyCode > 57) event.returnValue = false;"  type="text" class="form-control" name="precio_venta" value="{{ old('precio_venta') }}" required autofocus>

                                @if ($errors->has('precio_venta'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('precio_venta') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--id_tipo_usuario-->
                        <div class="form-group col-xs-6{{ $errors->has('id_proveedor') ? ' has-error' : '' }}">
                            <label for="id_proveedor" class="col-md-4 control-label">Proveedor</label>

                            <div class="col-md-7">
                                <select id="id_proveedor" name="id_proveedor" class="form-control" required="required">
                                    <option value="">Seleccione..</option>
                                    @foreach($proveedores as $value)
                                        @if ($value->estado != 0)
                                                <option value="{{$value->id}}">{{$value->nombrecomercial}}</option>
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

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Agregar
                                </button>
                            </div>
                        </div>
                        </div>
                    </form>
                </div>
            
        </div>
    </div>
</div>
@endsection
