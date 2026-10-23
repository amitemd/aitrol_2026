@extends('insumos.marca.base')
@section('action-content')

<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box box-primary"> 
                <div class="box-header with-border">
                    <div class="col-md-9">
                       <h3 class="box-title">Editar Marca</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
                    </div>
                </div>
                <form class="form-vertical" role="form" method="POST" action="{{ route('marca.update', ['id' => $marca->id]) }}">
                    <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">         
                        <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-xs-6 control-label">Nombre</label>
                            <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $marca->nombre }}" required autofocus>
                            @if ($errors->has('nombre'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('nombre') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-xs-8 control-label">Descripcion</label>
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $marca->descripcion }}" required autofocus>
                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-xs-6 control-label">Estado</label>
                            <select id="estado" name="estado" class="form-control">
                                <option {{$marca->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$marca->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>
                            </select>  
                            @if ($errors->has('estado'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('estado') }}</strong>
                                </span>
                            @endif
                        </div>
                        <div class="form-group col-xs-12">
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
</section> 
@endsection
