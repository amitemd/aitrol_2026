@extends('contable.nomina.base')
@section('action-content')
<div class="content">
    <div class="box">
        <div class="col-md-12 cabecera">
            <div class="box-header"><h3 class="box-title">Configurar fechas fondo de reserva anterior</h3></div>
            <form class="form-vertical" role="form" id="form_fr">
                {{ csrf_field() }}
                <div class="box-body dobra">
                <input type="hidden" name="id_nomina" value="{{$nomina->id}}">  
                <h5 style="color: red; padding-top: 10px; padding-left: 500px; padding-right: 50px; padding-bottom: 10px;"><b> {{trans('nomina.empleado')}}: {{$nomina->user->apellido1}} {{$nomina->user->apellido2}} {{$nomina->user->nombre1}} {{$nomina->user->nombre2}} </b></h5>
                    <div class="form-group col-xs-12{{ $errors->has('fecha_ingreso_anterior') ? ' has-error' : '' }}">
                        <label for="fecha_ingreso_anterior" class="col-md-2 control-label">Fecha ingreso anterior</label>
                        <div class="col-md-7">
                            <input  id="fecha_ingreso_anterior" type="date" class="form-control" name="fecha_ingreso_anterior" value="@if(!is_null($fr_anterior)){{$fr_anterior->fecha_ingreso_anterior}}@endif">
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('fecha_egreso_anterior') ? ' has-error' : '' }}">
                        <label for="fecha_egreso_anterior" class="col-md-2 control-label">Fecha salida anterior</label>
                        <div class="col-md-7">
                            <input  id="fecha_egreso_anterior" type="date" class="form-control" name="fecha_egreso_anterior" value="@if(!is_null($fr_anterior)){{$fr_anterior->fecha_egreso_anterior}}@endif">
                        </div>
                    </div>
                        
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary btn-gray">
                            @lang('agenda.guardar')
                            </button>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    function guardar(){
  
        var fecha_ing = $("#fecha_ingreso_anterior").val();
        var fecha_egr = $("#fecha_egreso_anterior").val();
      
        if(fecha_ing == "" || fecha_egr == ""){
            swal("Error!", "Por favor ingrese ambas fechas", "error");
        }else{
               $.ajax({
                url: "{{route('nuevo_rol.store_fr_anterior')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#form_fr').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('nomina.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 1000);
                    }
                },
                error: function(xhr, status) {
                    alert(`{{trans('laboratorio.existioproblema')}}`);
                    //console.log(xhr);
                },
            });
        }

    } 
</script>
@endsection