@extends('sala-mgmt.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">{{trans('sala-mgmt.editararea')}}: {{$mantenimientos_g->nombre_mantenimientos_g}}</h3>
            </div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('mantenimientos_oficinas.actualizar', ['id_unidad' => $mantenimientos_g->id, 'id_oficina' => $oficinas->id]) }}">
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">

                    <!--nombre_oficina-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">{{trans('sala-mgmt.editararea')}}</label>
                        <div class="col-md-7">
                            <input id="nombre_oficina" type="text" class="form-control" name="nombre_oficina" value="{{ $oficinas->nombre_oficina }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            <input id="id_unidad" type="hidden" class="form-control" name="id_unidad" value="{{ $mantenimientos_g->id }}">
                            @if ($errors->has('nombre_oficina'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre_oficina') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <!--Descripción-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">{{trans('tecnicof.description')}}</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $oficinas->descripcion }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('descripcion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('descripcion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>


                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-2 control-label">{{trans('sala-mgmt.estado')}}</label>
                        <div class="col-md-7">
                            <select id="estado" name="estado" class="form-control">
                                <option {{$oficinas->estado == 0 ? 'selected' : ''}} value="0">{{trans('sala-mgmt.inactivo')}}</option>
                                <option {{$oficinas->estado == 1 ? 'selected' : ''}} value="1">{{trans('sala-mgmt.activo')}}</option>
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
                                {{trans('sala-mgmt.actualizar')}}
                            </button>
                        </div>
                    </div>

                </div>
            </form>

        </div>

    </div>
</div>

@endsection