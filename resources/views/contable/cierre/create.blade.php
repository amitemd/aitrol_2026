@extends('contable.cierre.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Cierre</h3></div>
            <form class="form-vertical" role="form" id="formulario"  method="POST" action="{{route('cierre.mensual.store')}}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">                 
                    <!--Area-->
                    <div class="form-group col-xs-12{{ $errors->has('motivo') ? ' has-error' : '' }}">
                        <label for="motivo" class="col-md-2 control-label">Motivo</label>
                        <div class="col-md-7">
                            <input  id="motivo" type="text" class="form-control" name="motivo" value="{{ old('motivo') }}" required style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('motivo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('motivo') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('fecha') ? ' has-error' : '' }}">
                        <label for="fecha" class="col-md-2 control-label">Fecha</label>
                        <div class="col-md-7">
                            <input  id="fecha" required type="date" class="form-control" name="fecha" value="{{ old('fecha') }}" @if(!is_null($ultimo)) min="{{$ultimo}}" @endif >
                             @if ($errors->has('fecha'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>               
                        
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" type="button" class="btn btn-primary btn-gray">
                            @lang('agenda.agregar')
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

@endsection