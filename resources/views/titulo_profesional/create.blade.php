@extends('titulo_profesional.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">@lang('turnerof.aggtituprof')</h3></div>
            <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                   
                
                    <!--Area-->
                    <div class="form-group col-xs-12{{ $errors->has('titulo_universitario') ? ' has-error' : '' }}">
                        <label for="titulo_universitario" class="col-md-2 control-label">@lang('turnerof.tituprof')</label>
                        <div class="col-md-7">
                            <input  id="titulo_universitario" type="text" class="form-control" name="titulo_universitario" value="{{ old('titulo_universitario') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('titulo_universitario'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('titulo_universitario') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-12{{ $errors->has('titulo_prefijo') ? ' has-error' : '' }}">
                        <label for="titulo_prefijo" class="col-md-2 control-label">@lang('turnerof.preftitulo')</label>
                        <div class="col-md-7">
                            <input  id="titulo_prefijo" type="text" class="form-control" name="titulo_prefijo" value="{{ old('titulo_prefijo') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" >
                             @if ($errors->has('titulo_prefijo'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('titulo_prefijo') }}</strong>
                                    </span>
                                @endif
                        </div>
                    </div>
                    
                    <!--ESTADO 
                        <div class="form-group col-md-6">
                            <label for="estado_titulo" class="col-md-4 texto">Estado</label>
                            <div class="col-md-7">
                            <select id="estado_titulo" name="estado_titulo" class="form-control" required>
                                <option>Seleccione...</option>
                                <option value="1">Activo</option>
                                <option value="0">Inactivo</option>
                            </select>
                            </div>
                        </div> -->
               
                        
                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="guardar()" type="button" class="btn btn-primary btn-gray">
                            @lang('agenda.agregar')
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
  
        var descri = $("#titulo_universitario").val();
        var prefijo = $("#titulo_prefijo").val();
        var estado = $("#estado_titulo").val();
        if(descri == "" || estado == "" || prefijo==""){
            swal("Error!", "Campos Vacios", "error");
        }else{
               $.ajax({
                url: "{{route('tituloprofesional.store')}}",
                headers: {
                    'X-CSRF-TOKEN': $('input[name=_token]').val()
                },
                data: $('#formulario').serialize(),
                type: 'POST',
                dataType: 'json',
                success: function(data) {
                    var url = "{{route('tituloprofesional.index')}}"
                    if (data == 'ok') {
                        setTimeout(function() {
                            swal("Guardado!", "trans('turnerof.correcto')", "success");
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