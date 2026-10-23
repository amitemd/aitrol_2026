@extends('servicios_generales.limpieza_area.base')
@section('action-content')

<section class="content">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Servicios Generales</a></li>
            <li class="breadcrumb-item"><a href="../ambiente">Limpieza y Desinfección de Área</a></li>
            <li class="breadcrumb-item active" aria-current="page">Crear</li>
        </ol>
    </nav>
    <input type="hidden" value="{{$registro->id}}" name="id">
    <div class="box">
        <div class="box-header">
            <div class="col-md-5">
                <h5><b>Control de Limpieza y Desinfección de área Piso: </b></h5>
            </div>
            <div class="col-md-3">
                <select name="eleccion" id="elecion" class="form-control" readonly>
                    <option value="">Seleccione</option>
                    @foreach($oficinas as $value)
                    <option {{$registro->id_oficina == $value->id ? 'selected ': ''}} value="{{$value->id}}">{{$value->nombre_oficina}}</option>
                    @endforeach
                </select>

            </div>
            <div class="col-md-4 text-right">
                <button onclick="history.back()" class="btn btn-danger">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <form id="formulario" class="form-vertical" role="form" method="POST" action="{{route('edit_save_limpieza_area')}}" accept-charset="UTF-8" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input type="hidden" name="id" value="{{$registro->id}}">
            <div class="separator"></div>
            <div class="box-body">
                <div class="form-group col-md-12">


                    <label for="tipo_desinfeccion" class="col-md-2 control-label">Tipo de Desinfección</label>
                    <div class="col-md-4" style="padding-left: 10px;padding-right: 5%;">
                        <select name="tipo_desinfeccion" id="tipo_desinfeccion" class="form-control" readonly>
                            <option value="">Seleccione</option>
                            <option {{$registro->tipo_desinfeccion == 1 ? 'selected' : ''}} value="1">Concurrente</option>
                            <option {{$registro->tipo_desinfeccion == 0 ? 'selected' : ''}} value="0">Terminal</option>
                        </select>
                    </div>

                
                        <label for="accion_limpieza" class="col-md-2 control-label">¿Qué acción realizó?</q></label>
                        <div class="col-md-4" style="padding-left: 10px;padding-right: 5%;">
                            <select name="accion_limpieza" id="accion_limpieza" class="form-control" readonly>
                                <option value="">Seleccione</option>
                                <option {{$registro->accion_limpieza == 1 ? 'selected' : ''}} value="1">Limpieza</option>
                                <option {{$registro->accion_limpieza == 2 ? 'selected' : ''}} value="2">Desinfeccion</option>
                                <option {{$registro->accion_limpieza == 3 ? 'selected' : ''}} value="3">Limpieza y Desinfeccion</option>
                            </select>
                        </div>
            
                    <div>
                        &nbsp;
                    </div>

                    <div id="div_hc1" class="form-group col-lg-12 {{ $errors->has('archivo') ? ' has-error' : '' }}">
                        <label for="archivo" class="col-lg-6 control-label">Evidencia despues de la limpieza y desinfección del área (img)</label>
                        <div class="col-lg-6" style="padding-left: 10px;padding-right: 5%;">
                            <input id="file-input" name="evidencia_despues" type="file" / readonly>
                        </div>
                    </div>


                    <div class="form-group col-md-12">

                        <label for="insumos" class="col-md-2 control-label">Insumos</label>
                        <div class="col-md-3">
                            <select name="insumos[]" id="insumos" class="form-control js-example-basic-multiple" multiple="multiple" readonly>
                                <option value="">Seleccione</option>
                                @foreach($registro->insumos as $value)
                                @foreach($productos as $val)
                                <option {{$value->id_insumos == $val->id ? 'selected' : ''}} value="{{$val->id}}">{{$val->nombre}}</option>
                                @endforeach
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group col-md-12 {{ $errors->has('observaciones') ? ' has-error' : '' }}">
                        <label for="observaciones" id="titulo" class="col-md-2 control-label">Observaciones</label>
                        <div class="col-md-10" style="padding-left: 10px;padding-right: 5%;">
                            <input id="observaciones" type="text" class="form-control" readonly value="{{$registro->observaciones}}" name="observaciones" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary" id="guardar">
                                Guardar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
</section>

<script type="text/javascript">
    $(document).ready(function() {
        $(".js-example-basic-multiple").prop("disabled", true);
        $('.js-example-basic-multiple').select2();
    });
</script>



@endsection