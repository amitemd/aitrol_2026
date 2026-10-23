@extends('contable.porcentaje_retencion.base')
@section('action-content')

<section class="content">
    <div class="box " style="background-color: white;">
            <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
                <div class="col-md-9">
                    <h3 class="box-title">Actualizar Porcentajes de Retenciones del IVA (Bienes y Servicios)</h3>
                </div>
                
            <div class="box-body" style="background-color: #ffffff;">
                <div class="col-md-12" style="text-align: right;">
                          <a type="button" href="http://192.168.75.109/sis_medico_prb/public/contable/porcentaje_retencion" class="btn btn-primary btn-sm">
                          <span class="glyphicon glyphicon-arrow-left">Regresar</span>
                          </a>
                       </div>
                    </div>
            </div>
                <div class="box-body">
                        <input type="hidden" name="_method" value="PATCH">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <div class="form-group{{ $errors->has('nombre') ? ' has-error' : '' }}">
                            <label for="nombre" class="col-md-4 control-label">Nombre</label>
                            <div class="col-md-6">
                                <input id="nombre" type="text" class="form-control" name="nombre" value="{{ $bodega->nombre }}" required autofocus style="text-transform:uppercase;">

                                @if ($errors->has('nombre'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('nombre') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="form-group{{ $errors->has('id_hospital') ? ' has-error' : '' }}">
                            <label for="id_hospital" class="col-md-4 control-label">Hospital</label>
                            <div class="col-md-6">
                                <select class="form-control" name="id_hospital">
                                    @foreach($hospital as $value)
                                        <option value="{{$value->id}}" @if($bodega->id_hospital == $value->id) selected @endif >{{$value->nombre_hospital}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('estado') ? ' has-error' : '' }}">
                            <label for="estado" class="col-md-4 control-label">Estado</label>
                            <div class="col-md-6">
                                <select class="form-control" name="estado">
                                    <option {{$bodega->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                    <option {{$bodega->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>
                                </select>
                            </div>
                        </div>

                        <div class=" form-group{{ $errors->has('color') ? ' has-error' : '' }}">
                            <label for="color" class="col-md-4 control-label">Color de la Etiqueta</label>
                            <div class="col-md-6 colorpicker">
                                <input id="color" type="hidden" type="text" class="form-control" name="color" value="{{ $bodega->color }}" required>
                                <span class="input-group-addon colorpicker-2x"><i style="width: 50px; height: 50px;"></i></span> 
                                @if ($errors->has('color'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('color') }}</strong>
                                    </span>
                                @endif
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