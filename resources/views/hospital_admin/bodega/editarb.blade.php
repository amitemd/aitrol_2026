@extends('hospital_admin.base')
@section('action-content')

<section class="content">
  <div class ="box box-primary">
    <div class ="box-header">
      <div class ="row">
          <div class ="col-md-12">
            <h3 class="box-title">Actualizar Bodega</h3>
        </div>
    </div>

    <div class="box-body">
      <form class="form-vertical" role="form" method="GET" action="{{route('hospital_admin.updateb', ['id' => $bodegaid->id])}}">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <div class="col-md-12">
          <div class="row"> 
            <div class="col-md-4" style="padding-top: 20px">Nombre de la bodega</div>
            <div class="col-md-8"><input class = "form-control" type="text" value="{{$bodegaid->nombre}}" name="nombreb" required maxlength="30" style="border: 1px solid #BFC9CA; margin-top: 20px"></div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-4" style="padding-top: 20px">Ubicacion</div>
            <div class="col-md-8"><input class = "form-control" type="text" value="{{$bodegaid->ubicacion}}" name="ubicacion" required maxlength="200" style="border: 1px solid #BFC9CA; margin-top: 20px;"></div>
          </div>
        </div>

        <div class="col-md-12">
          <div class="row">
            <div class="col-md-4" style="padding-top: 20px">Color de la Etiqueta</div>
            <div class="col-md-8"><input class = "form-control" type="text" value="{{$bodegaid->color}}" name="color" value="{{$bodegaid->descripcion}}" required maxlength="200" style="border: 1px solid #BFC9CA; margin-top: 20px;"></div>
          </div>
        </div>
        
        <div class="col-md-12">
          <div class="row">
            <div class="col-md-4" style="padding-top: 20px">Estado</div>
              <div class="col-md-8"style="padding-top: 20px" >
                <select class="select form-control" id="estado" name="estado" style="margin-bottom: 25px;">
                  <option @if(($bodegaid->estado)==1) Selected @endif value="1">Piso 1</option>
                  <option @if(($bodegaid->estado)==2) Selected @endif value="2">Piso 2</option>
                  <option @if(($bodegaid->estado)==2) Selected @endif value="3">Piso 3</option>
                </select>
              </div>
          </div>
        </div>

        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
          <label for="color" class="col-md-4 control-label">Color de la Etiqueta</label>
          <div class="col-md-6 colorpicker">
              <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ $bodegaid->color }}" required>
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
            <button type="submit" class="btn btn-primary"><i class="fas fa-pen-square"></i> Actualizar</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
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