@extends('mantenimiento_nomina.tipo_rol.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Editar Tipo Rol</h3>
        </div>
        <div class="box-body">
            <form id="editar_plantilla" method="post" action="{{route('tipo_rol.update')}}">
                {{ csrf_field() }}
            
                <input type="hidden" name="id" id="id" value="{{$id}}">

                    
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-20 control-label"> Descripción Rol </label>
                        <input type="text" name="descripcion" size="20" class="form-control" value="{{$tipos_rol->descripcion}}" required maxlength="100">
                    </div>
                </div>

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('estado') ? ' has-error' : ''}}">
                        <label for="estado" class="col-md-20 control-label">Estado</label>
                        <select class="form-control" id="estado" name="estado"> 
                        <option {{$tipos_rol->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                        <option {{$tipos_rol->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option> 
                        </select >
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