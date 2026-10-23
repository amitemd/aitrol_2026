@extends('procedimiento.base')

@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nuevo Procedimiento</h3></div>
            <form class="form-vertical" role="form" method="POST" action="{{ route('procedimiento.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                    <!--nombre-->
                    <div class="form-group col-xs-12{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input id="nombre1" type="text" class="form-control" name="nombre" value="{{ old('nombre') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus>
                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
               
                    <!--observacion-->
                    <div class="form-group col-xs-12{{ $errors->has('observacion') ? ' has-error' : '' }}">
                        <label for="observacion" class="col-md-2 control-label">Observación</label>
                        <div class="col-md-7">
                            <input id="observacion" type="text" class="form-control" name="observacion" value="{{ old('observacion') }}" required autofocus>
                            @if ($errors->has('observacion'))
                            <span class="help-block">
                                <strong>{{ $errors->first('observacion') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="form-group col-xs-12{{ $errors->has('id_grupo_procedimiento') ? ' has-error' : '' }}">
                        <label for="id_grupo_procedimiento" class="col-md-2 control-label">Tipo Procedimiento </label>
                        <div class="col-md-7">
                          <select id="id_grupo_procedimiento"  type="number" name="id_grupo_procedimiento" class="form-control" >
                            <option value="0">Seleccione</option> 
                            @foreach($tprocedimientos as $tprocedimiento)
                            <option  value="{{$tprocedimiento->id}}">
                                {{$tprocedimiento->nombre}}</option>
                            @endforeach
                          </select>
                          @if ($errors->has('id_grupo_procedimiento'))
                            <span class="help-block">
                                <strong>{{ $errors->first('id_grupo_procedimiento') }}</strong>
                            </span>
                            @endif
                        </div>
                      </div>
                       
                        
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
