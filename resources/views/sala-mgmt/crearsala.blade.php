@extends('sala-mgmt.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Sala para Unidad: {{ $hospital->nombre_hospital }}</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('sala-management.grabar',['id' => $hospital->id]) }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--nombre_sala-->
                    <div class="form-group col-xs-10{{ $errors->has('nombre_sala') ? ' has-error' : '' }}">
                        <label for="nombre_sala" class="col-md-2 control-label">Nombre Sala</label>
                        <div class="col-md-7">
                            <input id="nombre_sala" type="text" class="form-control" name="nombre_sala" value="{{ old('nombre_sala') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre_sala'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre_sala') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>

                    <input id="id_hospital" type="hidden" class="form-control" name="id_hospital" value="{{ $hospital->id }}" >
               
                        
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
@endsection
