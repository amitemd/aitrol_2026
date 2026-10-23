@extends('rrhh.rubros_detalles.base')
@section('action-content')
<div class="container">
    <div class="row">
        <!--left-->
            <div class="box box-primary col-xs-24"> 
                <div class="box-header with-border"><h3 class="box-title">Editar Rubro</h3></div>
                <form id="frm" class="form-vertical" role="form" method="POST" action="{{ route('rubrosDetalles.update') }}">
                    <div class="box-body col-xs-24">
                        <input type="hidden" name="_method" value="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ $rubrosDetalleNomina->id }}">
     
                        <!--descripcion-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">Descripcion</label>
                        <div class="col-md-9">
                            <textarea id="descripcion" type="text" class="form-control" name="descripcion" required autofocus>{{ $rubrosDetalleNomina->descripcion }}</textarea>
                        </div> 
                    </div>
                    
                    <!--descripcion_cuenta-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion_cuenta') ? ' has-error' : '' }}">
                        <label for="descripcion_cuenta" class="col-md-2 control-label">Descripcion cuenta </label>
                        <div class="col-md-9">
                            <textarea id="descripcion_cuenta" type="text" class="form-control" name="descripcion_cuenta" required autofocus>{{ $rubrosDetalleNomina->descripcion_cuenta }}</textarea>
                        </div> 
                    </div>


                    <div class="form-group col-xs-12">
                        <!--ingreso_egreso-->
                        <label for="ingreso_egreso" class="col-md-2 control-label">Ingreso / Egreso</label>
                        <div class="col-md-3">
                            <select id="ingreso_egreso" name="ingreso_egreso" class="form-control" required="required">
                                <option @if($rubrosDetalleNomina->ingreso_egreso == 0) selected @endif value="0">Ingreso</option>
                                <option @if($rubrosDetalleNomina->ingreso_egreso == 1) selected @endif value="1">Egreso</option>
                            </select>
                            @if ($errors->has('ingreso_egreso'))
                            <span class="help-block">
                                <strong>{{ $errors->first('ingreso_egreso') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!--debe_haber-->
                        <label for="debe_haber" class="col-md-2 control-label">Debe / Haber</label>
                        <div class="col-md-3">
                            <select id="debe_haber" name="debe_haber" class="form-control" required="required">
                                <option @if($rubrosDetalleNomina->debe_haber == 0) selected @endif value="0">Debe</option>
                                <option @if($rubrosDetalleNomina->debe_haber == 1) selected @endif value="1">Haber</option>
                            </select>
                            @if ($errors->has('debe_haber'))
                            <span class="help-block">
                                <strong>{{ $errors->first('debe_haber') }}</strong>
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group col-xs-12">
                        <!--editable-->
                        <label for="ingreso_egreso" class="col-md-2 control-label">Editable</label>
                        <div class="col-md-3">
                            <select id="editable" name="editable" class="form-control" required="required">
                                <option @if($rubrosDetalleNomina->editable == 0) selected @endif value="0">No</option>
                                <option @if($rubrosDetalleNomina->editable == 1) selected @endif value="1">Si</option>
                            </select>
                            @if ($errors->has('editable'))
                            <span class="help-block">
                                <strong>{{ $errors->first('editable') }}</strong>
                            </span>
                            @endif
                        </div>
                        <!--marca-->
                        <label for="marca" class="col-md-2 control-label">Marca</label>
                        <div class="col-md-3">
                            <input id="marca" type="text" class="form-control" name="marca" value="{{ $rubrosDetalleNomina->marca }}" required autofocus />
                        </div>
                    </div>

                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button id="boton_guardar" type="button" onclick="actualizar()" class="btn btn-primary">
                                Actualizar
                                </button>
                            </div>
                        </div>
                    </div>    
                </form>
            </div>  
    </div>
</div>    

@endsection
<script type="text/javascript">
    function goBack() {
        window.history.back();
    }

    function actualizar() {
        document.getElementById('boton_guardar').disabled = true;
        var descripcion = $('#descripcion').val();
        var descripcion_cuenta = $('#descripcion_cuenta').val();
        var marca = $('#marca').val();
        var msj = "";

        if(descripcion==""){
            msj = msj + "Ingrese la descripcion <br>";
        }

        if(descripcion_cuenta==""){
            msj = msj + "Ingrese la descripcion de la cuenta <br>";
        }

        if(marca==""){
            msj = msj + "Ingrese la marca <br>";
        }
        
        if (msj == "") {
            $.ajax({
                type: 'post',
                url: "{{route('rubrosDetalles.update')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#frm').serialize(),
                success: function(data) {
                    if (data.status == "success") {
                        Swal.fire("Success!", data.msj, "success");
                        setTimeout(function() {
                            window.location.href = "{{ route('rubrosDetalles.index')}}";
                        }, 1500)
                    } else {
                        Swal.fire("Error!", data.msj, "error");
                        document.getElementById('boton_guardar').disabled = false;
                    }
                },
                error: function(data) {
                    document.getElementById('boton_guardar').disabled = false;
                }
            });
        } else {
            alertas("error", "Porfavor llenes los siguientes campos", msj);
            document.getElementById('boton_guardar').disabled = false;
        }
    }
</script>