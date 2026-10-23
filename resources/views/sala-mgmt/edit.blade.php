@extends('tipo_usuario-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar tipo de usuario</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('tipo_usuario-management.update', ['id' => $tipo_usuarios->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                        
                    <!--nombre-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $tipo_usuarios->nombre }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            <input id="nombre_db" type="hidden" class="form-control" name="nombre_db" value="{{ $tipo_usuarios->nombre }}"  style="text-transform:uppercase;" >
                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                                            
                    <!--descripcion-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">Descripcion</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $tipo_usuarios->descripcion }}" required autofocus>
                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                            @endif
                        </div>        
                    </div>
                        
                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$tipo_usuarios->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$tipo_usuarios->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
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
