@extends('mantenimiento_nomina.area_rh.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Agregar Nueva Area</h3></div>
            <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                
                    <!--Area-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">Área</label>
                        <div class="col-md-7">
                            <input  id="descripcion" type="text" class="form-control" name="descripcion" value="{{ old('descripcion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('descripcion'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('descripcion') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    
                    <!--ESTADO -->
                        <div class="form-group col-md-6">
                            <label for="estado_area" class="col-md-4 texto">Estado</label>
                            <div class="col-md-7">
                            <select id="estado_area" name="estado_area" class="form-control" required>
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
  
        var descri = $("#descripcion").val();
        var estado = $("#estado_area").val();
        if(descri == "" || estado == ""){
            swal("Error!", "Campos Vacios", "error");
        }else{
               $.ajax({
                url: "{{route('area_rh.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('area_rh.index')}}"
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