@extends('preanestesico.preanestesico')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('Crear')}}</h3>
        </div>
        <div class="box-body">
            <form id="crear_protocolo" method="post" action="{{route('formatoconsent.store')}}">
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('diagnostico') ? ' has-error' : '' }}">
                        <label for="diagnostico" class="col-md-20 control-label">Diagnostico</label>
                        <input type="text" name="diagnostico" id="diagnostico" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('tiempo de evolucion') ? ' has-error' : '' }}">
                        <label for="tiempo_evo" class="col-md-20 control-label">Tiempo De Evolucion </label>
                        <input type="text" name="tiempo_evo"  id="tiempo_evo" size="20" class="form-control" value="" required maxlength="255">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('tratamiento') ? ' has-error' : '' }}">
                        <label for="tratamiento" class="col-md-20 control-label">Tratamiento</label>
                        <input type="text" name="tratamiento" size="20" class="form-control" value="" required maxlength="255">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('Agregar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection