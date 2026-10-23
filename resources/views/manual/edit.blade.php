@extends('manual.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Editar Manual</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('manual.update', ['id' => $manual->id]) }}">
                  
                            <input type="hidden" name="_method" value="PATCH">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                <label for="nombre" class="col-md-4 control-label">Nombre</label>

                                <div class="col-md-6">
                                    <input id="nombre" type="text" class="form-control" name="nombre" value="{{$manual->nombre}}" required autofocus>

                                    @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                                <label for="descripcion" class="col-md-4 control-label">Descripción</label>

                                <div class="col-md-6">
                                    <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{$manual->descripcion}}" required>

                                    @if ($errors->has('descripcion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div class="form-group{{ $errors->has('fecha_inicio') ? ' has-error' : '' }}">
                                <label for="fecha_inicio" class="col-md-4 control-label">Fecha inicio</label>

                                <div class="col-md-4">
                                    <input id="fecha_inicio" name="fecha_inicio" type="date" class="form-control" value="{{date('Y-m-d',strtotime($manual->fecha_inicio))}}" required autofocus>
                                </div>

                            </div>

                            <div class="col-md-12">
                                &nbsp;
                            </div>

                            <div class="form-group{{ $errors->has('fecha_fin') ? ' has-error' : '' }}">
                                <label for="fecha_fin" class="col-md-4 control-label">Fecha Expiración</label>
                                <div class="col-md-4">
                                    <input id="fecha_fin" name="fecha_fin" type="date" class="form-control" value="{{date('Y-m-d',strtotime($manual->fecha_fin))}}" required autofocus>
                                </div>

                            </div>
                            <div class="col-md-12">
                                &nbsp;
                            </div>
                            <div>
                                <div style="text-align:center;">
                                    <button type="submit" class="btn btn-primary">
                                        Actualizar
                                    </button>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>



    @endsection