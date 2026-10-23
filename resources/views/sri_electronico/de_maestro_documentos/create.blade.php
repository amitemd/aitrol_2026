@extends('sri_electronico.de_maestro_documentos.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('maestroDocumentos.Crear')}}</h3>
        </div>
        <div class="box-body">
            <form id="crear_protocolo" method="post" action="{{route('demaestrodoc.store')}}">
                {{ csrf_field() }}
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">{{trans('maestroDocumentos.Nombre_Documento')}}</label>
                        <input type="text" name="nombre" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-20 control-label">{{trans('maestroDocumentos.Codigo')}} </label>
                        <input type="text" name="codigo" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-20 control-label"> {{trans('maestroDocumentos.Estado')}}</label>
                        <input type="text" name="estado" size="20" class="form-control" value="" readonly>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('maestroDocumentos.Agregar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection