@extends('hospital_admin.base')
@section('action-content')

<section class="content">
    <div class ="box box-primary">
        <div class ="box-header">
            <div class ="row">
                <div class ="col-md-12">
                    <h3 class="box-title">Agregar nueva Bodega</h3>
                </div>
            </div>
        </div>
    
        <div class="box-body">
            <form action="{{route('hospital_admin.agregarb')}}" enctype="multipart/form-data" method="POST">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <div class="col-md-12">
                            <div class="row"> 
                                <div class="col-md-4" style="padding-top: 20px">Nombre de la Bodega</div>
                                <div class="col-md-8"><input class = "form-control" type="text" name="nombreb" required maxlength="30" style="border: 1px solid #BFC9CA; margin-top: 20px"></div>
                            </div>
                    </div>

                    <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-4" style="padding-top: 20px">Ubicacion</div>
                                <div class="col-md-8 "><input class = "form-control" type="text" name="ubicacion" required maxlength="200" style="border: 1px solid #BFC9CA; margin-top: 20px;"></div>
                            </div>
                    </div>

                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-4" style="padding-top: 20px">Piso</div>
                            <div class="col-md-8"style="padding-top: 20px" >
                                <select class="select form-control" name="estado" required>
                                    <option value="1">PISO 1</option>
                                    <option value="2">PISO 2</option>
                                    <option value="3">PISO 3</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                        <label for="color" class="col-md-4 control-label">Color de la Etiqueta</label>
                        <div class="col-md-6 colorpicker">
                            <input id="color" type="hidden" type="text" class="form-control" name="color"  value="@if(!is_null(old('color'))) {{ old('color') }} @else #000000 @endif" required >
                            <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span> 
                            @if ($errors->has('color'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('color') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-12" style="text-align: center; margin-top: 20px;">
                        <a href="{{route('hospital_admin.bodega') }}" class="btn btn-danger"><i class="far fa-trash-alt"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-plus"></i> Agregar</button>
                    </div>
            </form>
        </div>
    </div>
</section>
<style>
    .colorpicker-2x .colorpicker-saturation {
        width: 200px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-hue,
    .colorpicker-2x .colorpicker-alpha {
        width: 30px;
        height: 200px;
    }

    .colorpicker-2x .colorpicker-color,
    .colorpicker-2x .colorpicker-color div {
        height: 30px;
    }
</style>
@endsection