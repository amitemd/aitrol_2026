@extends('sala-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar Sala de Unidad: {{$hospital->nombre_hospital}}</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('sala-management.actualizar', ['id_hospital' => $hospital->id, 'id_sala' => $salas->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">       
                        
                    <!--nombre_sala-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre Sala</label>
                        <div class="col-md-7">
                            <input id="nombre_sala" type="text" class="form-control" name="nombre_sala" value="{{ $salas->nombre_sala }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            <input id="id_hospital" type="hidden" class="form-control" name="id_hospital" value="{{ $hospital->id }}" >
                            @if ($errors->has('nombre_sala'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre_sala') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                                            
                        
                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$salas->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$salas->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
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
