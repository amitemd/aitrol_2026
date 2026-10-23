@extends('contable.tipo_acreedor.base')
@section('action-content')

<section class="content">
    <div class="row">
        <div class="col-md-12 col-xs-24" >
            <div class="box">
                <div class="box-header">
                    <div class="col-md-9">
                      <h3 class="box-title">Agregar Nuevo Tipo de Proveedor</h3>
                    </div>
                    <div class="col-md-3" style="text-align: right;">
                        <a type="button" href="{{URL::previous()}}" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left"> Regresar</span>
                        </a>
                    </div>
                </div>
                <div class="box-body">
                    <form class="form-vertical" role="form" method="POST" action="{{ route('tipoacreedor.store') }}">
                    {{ csrf_field() }}
                        <div class="box-body dobra col-xs-24">
                            <div class="form-group col-xs-10{{ $errors->has('nombre') ? ' has-error' : '' }}">
                                <label for="nombre" class="col-md-4 control-label">Nombre</label>
                                <div class="col-md-7">
                                    <input id="nombre" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                    @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <div class="form-group col-xs-10{{ $errors->has('descripcion') ? ' has-error' : '' }}">
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
                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-primary btn-gray">
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
</section>
@endsection
