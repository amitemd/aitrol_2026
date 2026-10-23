@extends('hospital-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Unidad de Atención</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('hospital-management.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--nombre hospital-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre_hospital') ? ' has-error' : '' }}">
                        <label for="nombre_hospital" class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre_hospital" type="text" class="form-control" name="nombre_hospital" value="{{ old('nombre_hospital') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
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
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
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
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
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
                            <input id="direccion" type="text" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required autofocus>
                            @if ($errors->has('telefono1'))
                            <span class="help-block">
                                <strong>{{ $errors->first('telefono1') }}</strong>
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
@endsection
