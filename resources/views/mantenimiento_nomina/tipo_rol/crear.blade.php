@extends('mantenimiento_nomina.tipo_rol.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Crear Tipo Rol</h3>
        </div>
        <div class="box-body">
            <form id="crear_plantilla" method="post" action="{{route('tipo_rol.store')}}">
                {{ csrf_field() }}
            
                             
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion_tipo') ? ' has-error' : '' }}">
                        <label for="descripcion_tipo" class="col-md-20 control-label">Descripción Rol</label>
                        <input type="text" name="descripcion_tipo" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>
                            

                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Agregar
                        </button>
                    </div>
                </div>   
                
                
            </form>
        </div>
    </div>
</section>

@endsection