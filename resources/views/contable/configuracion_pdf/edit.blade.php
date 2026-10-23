@extends('contable.configuracion_pdf.base')
@section('action-content')

<script>
    function goBack() {
        location.href = "{{route('configuraciones_pdf_index')}}";
    }
</script>

<section class="content">
    <div class="box" style="background-color: white;">
        <div class="box-header color_cab with-border" style="color: black; font-family: 'Helvetica general3';border-style:none;margin:none;">
            <div class="col-md-11">
                <h5><b>Configuracion PDF</h5>
            </div>
            <div class="col-md-1 text-right">
                <button onclick="goBack()" class="btn btn-danger btn-gray">
                    <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{route('actualizar_pdf')}}">
                {{ csrf_field() }}
                <div class="box-body dobra">
                    <input type="hidden" name="id" id="id" value="{{$edit->id}}">
                    <div class="form-group  col-md-3">
                        <label for="fecha" class="col-md-2 texto">Fecha:</label>
                        <div class="col-md-12">
                            <input id="fecha" name="fecha" type="date" value="{{$edit->fech_autorizacion}}" class="form-control">
                        </div>
                    </div>
                    <div class="form-group  col-md-3" style="display: block;float:left !important">
                        <label for="empresa" class="col-md-6 texto">Estado:</label>
                        <div class="col-md-12">
                            <select name="estado" id="esatdo" class="form-control" style="width:100%;">
                                <option {{ $edit->estado == 1 ? 'selected' : ''}} value="1">Activo</option>
                                <option {{ $edit->estado == 0 ? 'selected' : ''}} value="0">Inactivo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group  col-md-3" style="display: block;float:left !important">
                        <label for="detalle" class="col-md-3 texto">Detalle:</label>
                        <div class="col-md-12">
                            <textarea class="form-control" name="detalle" id="detalle" style="height: 35px;width:100%;">{{$edit->detalle}}</textarea>
                        </div>
                    </div>
                    <div class="form-group  col-md-3" style="display: block;float:left !important">
                        <label for="detalle" class="col-md-3 texto">Autorizacion:</label>
                        <div class="col-md-12">
                            <input class="form-control" name="autorizacion" id="autorizacion" style="width:100%;" value="{{$edit->autorizacion}}">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group col-xs-10 text-center">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-default btn-gray btn_add">
                                    <i class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></i>&nbsp;&nbsp;Actualizar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>


@endsection