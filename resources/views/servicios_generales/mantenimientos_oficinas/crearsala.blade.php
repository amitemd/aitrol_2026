@extends('sala-mgmt.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header">
                <h3 class="box-title">{{trans('sala-mgmt.agregarnuevaarea')}}: {{ $mantenimientos_g->nombre_unidad }}</h3>
            </div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('mantenimientos_oficinas.grabar',['id' => $mantenimientos_g->id]) }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">

                    <!--nombre_oficina-->
                    <div class="form-group col-xs-10{{ $errors->has('nombre_oficina') ? ' has-error' : '' }}">
                        <label for="nombre_oficina" class="col-md-2 control-label">{{trans('sala-mgmt.nombrearea')}}</label>
                        <div class="col-md-7">
                            <input id="nombre_oficina" type="text" class="form-control" name="nombre_oficina" value="{{ old('nombre_oficina') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                            @if ($errors->has('nombre_oficina'))
                            <span class="help-block">
                                <strong>{{ $errors->first('nombre_oficina') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <input id="id_unidad" type="hidden" class="form-control" name="id_unidad" value="{{ $mantenimientos_g->id }}">


                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                {{trans('sala-mgmt.agregar')}}
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@endsection