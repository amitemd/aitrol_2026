@extends('mantenimiento_nomina.horario.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Horario Laboral</h3></div>
            <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                
                    <!--Area-->
                    <div class="form-group col-xs-12{{ $errors->has('horario') ? ' has-error' : '' }}">
                        <label for="horario" class="col-md-2 control-label">Horario Laboral</label>
                        <div class="col-md-7">
                            <input  id="horario" type="text" class="form-control" name="horario" value="{{ old('horario') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('horario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('horario') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    
                    <!--ESTADO -->
                        <div class="form-group col-md-6">
                            <label for="estado_horario" class="col-md-4 texto">Estado</label>
                            <div class="col-md-7">
                            <select id="estado_horario" name="estado_horario" class="form-control" required>
                                <option>Seleccione...</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            </div>
                        </div>
               
                        
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary btn-gray">
                                Agregar
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
  
        var descri = $("#horario").val();
        var estado = $("#estado_horario").val();
        if(descri == "" || estado == ""){
            swal("Error!", "Campos Vacios", "error");
        }else{
               $.ajax({
                url: "{{route('mantenimiento.horario.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('mantenimiento.horario.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "Correcto", "success");
                            window.location = url;
                        }, 1000);
                    }
                },
                error: function(xhr, status) {
                    alert('Existió un problema');
                    //console.log(xhr);
                },
            });
        }

    } 
</script>
@endsection