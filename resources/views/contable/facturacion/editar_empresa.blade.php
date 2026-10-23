<div class="box box-warning box-solid">
    <div class="header box-header with-border" >
        <div class="box-title col-md-12" ><b style="font-size: 16px;">EDITAR EMPRESA</b></div>
        <div class="box-title col-md-12" ><b style="font-size: 16px;">{{$empresa->razonsocial}}</b></div>
        <div class="box-title col-md-12"><b>RUC: {{$empresa->id}}</b></div>
    </div>
	<form class="form-vertical" role="form" method="POST" action="{{ route('empresa.update', ['id' => $empresa->id]) }}">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                        <!--RUC-->
                        <div class="form-group col-xs-6{{ $errors->has('id') ? ' has-error' : '' }}">
                            <label for="id" class="col-xs-6 control-label">RUC</label>
                            <input id="id" type="text" class="form-control" name="id" value="{{ $empresa->id }}" required autofocus>
                                @if ($errors->has('id'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('id') }}</strong>
                                    </span>
                                @endif
                        </div>
                                            
                        <!--Razón Social-->
                        <div class="form-group col-xs-6{{ $errors->has('razonsocial') ? ' has-error' : '' }}">
                            <label for="razonsocial" class="col-xs-6 control-label">Razón Social</label>
                            <input id="razonsocial" type="text" class="form-control" name="razonsocial" value="{{ $empresa->razonsocial }}" required autofocus>
                                @if ($errors->has('razonsocial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('razonsocial') }}</strong>
                                    </span>
                                @endif
                        </div>
                        
                        <!--Nombre Comercial-->
                        <div class="form-group col-xs-6{{ $errors->has('nombrecomercial') ? ' has-error' : '' }}">
                            <label for="nombrecomercial" class="col-xs-8 control-label">Nombre Comercial</label>
                            <input id="nombrecomercial" type="text" class="form-control" name="nombrecomercial" value="{{ $empresa->nombrecomercial }}" required autofocus>
                                @if ($errors->has('nombrecomercial'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombrecomercial') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--Ciudad-->
                        <div class="form-group col-xs-6{{ $errors->has('ciudad') ? ' has-error' : '' }}">
                            <label for="ciudad" class="col-xs-6 control-label">Ciudad</label>
                            <input id="ciudad" type="text" class="form-control" name="ciudad" value="{{ $empresa->ciudad }}" required>
                                @if ($errors->has('ciudad'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('ciudad') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--direccion-->
                        <div class="form-group col-xs-6{{ $errors->has('direccion') ? ' has-error' : '' }}">
                            <label for="direccion" class="col-xs-6 control-label">Dirección</label>
                            <input id="direccion" type="text" class="form-control" name="direccion" value="{{ $empresa->direccion }}" required>
                                @if ($errors->has('direccion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('direccion') }}</strong>
                                    </span>
                                @endif
                        </div>
                                                    
                        <!--email-->
                        <div class="form-group col-xs-6{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-xs-6 control-label">E-mail</label>
                            <input id="email" type="text" class="form-control" name="email" value="{{ $empresa->email }}" required>
                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <!--telefono1-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono1') ? ' has-error' : '' }}">
                            <label for="telefono1" class="col-xs-8 control-label">Telefono Domicilio</label>
                            <input id="telefono1" type="text" class="form-control" name="telefono1" value="{{ $empresa->telefono1 }}" required>
                                @if ($errors->has('telefono1'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono1') }}</strong>
                                    </span>
                                @endif
                        </div>
                           
                        <!--telefono2-->
                        <div class="form-group col-xs-6{{ $errors->has('telefono2') ? ' has-error' : '' }}">
                            <label for="telefono2" class="col-xs-6 control-label">Telefono Celular</label>
                            <input id="telefono2" type="text" class="form-control" name="telefono2" value="{{ $empresa->telefono2 }}" required>
                                @if ($errors->has('telefono2'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('telefono2') }}</strong>
                                    </span>
                                @endif
                        </div>

                        <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-xs-6 control-label">Estado</label>
                            <select id="estado" name="estado" class="form-control">
                            <option {{$empresa->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                            <option {{$empresa->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>
                                        
                                </select>  
                                @if ($errors->has('estado'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('estado') }}</strong>
                                    </span>
                                @endif
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