@extends('hospital-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar Unidades de Atención</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('hospital-management.update', ['id' => $hospitales->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                        
                    <!--nombre_hospital-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre_hospital') ? ' has-error' : '' }}">
                        <label for="nombre_hospital" class="col-md-2 control-label">Nombre Unidad</label>
                        <div class="col-md-7">
                            <input id="nombre_hospital" type="text" class="form-control" name="nombre_hospital" value="{{ $hospitales->nombre_hospital }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('nombre_hospital'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre_hospital') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                                            
                    <!--ciudad-->
                    <div class="form-group col-xs-12{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                        <label for="ciudad" class="col-md-2 control-label">Ciudad</label>
                        <div class="col-md-7">
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $hospitales->ciudad }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>

                    <!--direccion-->
                    <div class="form-group col-xs-12{{ $errors->has('direccion') ? ' has-error' : '' }}">
                        <label for="direccion" class="col-md-2 control-label">Dirección</label>
                        <div class="col-md-7">
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $hospitales->direccion }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('direccion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('direccion') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>

                    <!--telefono1-->
                    <div class="form-group col-xs-12{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                        <label for="telefono1" class="col-md-2 control-label">Teléfono</label>
                        <div class="col-md-7">
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $hospitales->telefono1 }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('telefono1'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('telefono1') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                        
                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$hospitales->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$hospitales->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                            </select>  
                            @if ($errors->has('estado'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
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
