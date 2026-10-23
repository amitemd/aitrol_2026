@extends('sri_electronico.de_maestro_documentos.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">{{trans('maestroDocumentos.Editar_Maestro_Documento')}}</h3>
        </div>
        <div class="box-body">
            <form id="editar_protocolo" method="post" action="{{route('demaestrodoc.update')}}">
                {{ csrf_field() }}
                <input type="hidden" name="id" id="id" value="{{$id}}">

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">{{trans('maestroDocumentos.Nombre')}} </label>
                        <input type="text" name="nombre" size="20" class="form-control" value="{{$de_maestro->nombre}}" required maxlength="100">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-20 control-label">{{trans('maestroDocumentos.Codigo')}} </label>
                        <input type="text" name="codigo" size="20" class="form-control" value="{{$de_maestro->codigo}}" required maxlength="100">
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-20 control-label"> {{trans('maestroDocumentos.Estado')}} </label>
                        <input type="text" name="estado" size="20" class="form-control" value="{{$de_maestro->estado}}" readonly>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            {{trans('maestroDocumentos.Actualizar')}}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

@endsection