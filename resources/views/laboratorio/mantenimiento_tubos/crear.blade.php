@extends('laboratorio.mantenimiento_tubos.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Crear Tipo Tubo</h3>
        </div>
        <div class="box-body">
            <form id="crear_plantilla" method="post" action="{{route('tipo_tubo.store')}}">
                {{ csrf_field() }}
            
                             
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('nombre_tubo') ? ' has-error' : '' }}">
                        <label for="nombre_tubo" class="col-md-20 control-label">Tipo Tubo</label>
                        <input type="text" name="nombre_tubo" size="20" class="form-control" value=" " required maxlength="100">
                    </div>
                </div>

                <div class ="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('tubo_color') ? ' has-error' : '' }}">
                        <label for="tubo_color" class="col-md-5 control-label">Color del tubo</label>
                        <div class="col-md-5 col-md-offset-3 colorpicker colorpicker-element">
                             <input id="tubo_color" type="hidden" class="col-md-10 form-control" name="tubo_color" value="">
                            <span class="input-group-addon colorpicker-2x"><i style="width: 80px; height: 80px; background-color: rgb(0, 0, 0);"></i></span>         
                        </div> 
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