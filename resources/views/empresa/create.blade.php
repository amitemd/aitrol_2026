@extends('empresa.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Empresa</h3></div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('empresa.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--RUC-->
                    <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                        <label for="id" class="col-md-4 control-label">RUC</label>
                        <div class="col-md-7">
                            <input id="id" type="text" class="form-control" name="id" value="{{ old('id') }}" style="text-transform:uppercase;"  maxlength="13" required autofocus onkeyup="validarCedula(this.value);">
                            @if ($errors->has('id'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('id') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
               
                    <!--Razon Social-->
                    <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                        <label for="razonsocial" class="col-md-4 control-label">Razón Social</label>
                        <div class="col-md-7">
                            <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ old('razonsocial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('razonsocial'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('razonsocial') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
           
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-md-4 control-label">Nombre Comercial</label>
                            <div class="col-md-7">
                                <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ old('nombrecomercial') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombrecomercial'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-md-4 control-label">Ciudad</label>
                            <div class="col-md-7">
                                <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ old('ciudad') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('ciudad'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('ciudad') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!--Direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-md-4 control-label">Direccion</label>

                            <div class="col-md-7">
                                <input id="direccion" type="text" class="form-control" name="direccion" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>

                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                 
                        <!--email-->                        
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail</label>

                            <div class="col-md-7">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-md-4 control-label">Telefono Domicilio</label>

                            <div class="col-md-7">
                                <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ old('telefono1') }}" required autofocus>

                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-md-4 control-label">Telefono Celular</label>

                            <div class="col-md-7">
                                <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ old('telefono2') }}" required autofocus>

                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
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
