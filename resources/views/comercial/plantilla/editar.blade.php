@extends('comercial.plantilla.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header">
        </div>
        <div class="box-body">
            <form id="crear_plantilla" method="post" action="{{route('proforma.update_plantilla')}}">
                {{ csrf_field() }}
            
                <input type="hidden" name="id" id="id" value="{{$agrupador->id}}">
            
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-20 control-label">Codigo</label>
                        <input type="text" name="codigo_plantilla" size="20" class="form-control" value="@if(!is_null($agrupador)){{$agrupador->codigo}}@endif" required maxlength="100">
                    </div>
                </div>
                    
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre') ? ' has-error' : '' }}">
                        <label for="nombre" class="col-md-20 control-label">Nombre</label>
                        <input type="text" name="nombre_plantilla" size="20" class="form-control" value="@if(!is_null($agrupador)){{$agrupador->nombre}}@endif" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : '' }}">
                        <label for="estado" class="col-md-20 control-label">Estado</label>
                        <select id="estado_plantilla" name="estado_plantilla" class="form-control" required>
                            <option {{ $agrupador->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                            <option {{ $agrupador->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                        </select>
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
</section>

@endsection