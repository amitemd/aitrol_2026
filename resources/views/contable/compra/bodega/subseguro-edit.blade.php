@extends('seguro.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Actualizar Subseguro</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST" action="{{ route('subseguro.update')}}">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" class="form-control" name="horacrea" value="{{ $subseguro->created_at }}">
                        <input id="nombre1" type="hidden" class="form-control" name="nombre1" value="{{ $subseguro->nombre }}" >
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-6">
                                <input id="id" type="hidden" class="form-control" name="id" value="{{ $subseguro->id }}">                <input id="id_seguro" type="hidden" class="form-control" name="id_seguro" value="{{ $subseguro->id_seguro }}">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $subseguro->nombre }}" required autofocus style="text-transform:uppercase;">

                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                            <label for="descripcion" class="col-md-4 control-label">Descripcion</label>

                            <div class="col-md-6">
                                <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $subseguro->descripcion }}" required>

                                @if ($errors->has('descripcion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
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