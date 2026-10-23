@extends('rrhh.rubros_detalles.base')
@section('action-content')
<div class="container">
    <div class="row">
        <!--left-->
            <div class="box box-primary col-xs-24"> 
                <div class="box-header with-border"><h3 class="box-title">Asignar Cuenta a Rubro</h3></div>
                <form id="frm" class="form-vertical" role="form">
                    <div class="box-body col-xs-24">
                        <input type="hidden" name="_method" value="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" name="id" value="{{ $rubrosDetalleNomina->id }}">
                    
                        <!--descripcion_cuenta-->
                        <div class="form-group col-xs-12{{ $errors->has('descripcion_cuenta') ? ' has-error' : '' }}">
                            <label for="descripcion_cuenta" class="col-md-2 control-label">Descripcion cuenta </label>
                            <div class="col-md-6">
                                <textarea id="descripcion_cuenta" type="text" class="form-control" name="descripcion_cuenta" readonly>{{ $rubrosDetalleNomina->descripcion_cuenta }}</textarea>
                            </div> 
                        </div>


                        <div class="form-group col-xs-12">
                            <!--cuentas-->
                            <label for="cuentas" class="col-md-2 control-label">Cuenta contable</label>
                            <div class="col-md-6">
                                <select id="cuentas" name="cuentas" class="form-control select2" required="required">
                                    <option value="">...Seleccione...</option>
                                    @foreach($cuentas as $value)
                                        <option value="{{$value->id}}">{{$value->pempresa->plan}} - {{$value->pempresa->nombre}}</option>
                                    @endforeach
                                </select>
                                @if ($errors->has('cuentas'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('cuentas') }}</strong>
                                </span>
                                @endif
                            </div>                        
                        </div>

                        <div class="form-group col-xs-6">
                            <div class="col-md-6 col-md-offset-4">
                                <button id="boton_guardar" type="button" onclick="asignar_cuenta()" class="btn btn-primary">
                                Asignar Cuenta
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>  
    </div>
</div>    

@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0/js/select2.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
        $('.select2').select2({
            width: '100%', // Establece el ancho del select
            placeholder: 'Seleccione...', // Mensaje de placeholder
            search: true // Habilita la función de búsqueda
        });
    });

    function goBack() {
        window.history.back();
    }

    function asignar_cuenta() {
        document.getElementById('boton_guardar').disabled = true;
        var cuenta = $("#cuentas").val();
        var msj = "";

        if(cuenta==""){
            msj = msj + "Seleccione la cuenta <br>";
        }
        
        if (msj == "") {
            $.ajax({
                type: 'post',
                url: "{{route('rubrosDetalles.storeCuenta')}}",
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