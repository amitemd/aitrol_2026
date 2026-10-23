@extends('titulo_profesional.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">@lang('turnerof.edittituuni')</h3></div>
             <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    
                    <input type="hidden" id="id" name="id" value="{{$id}}">       
                        
                    <!--Nombre del area-->
                    <div class="form-group col-xs-12{{ $errors->has('titulo_universitario') ? ' has-error' : '' }}">
                        <label for="titulo_universitario" class="col-md-2 control-label">@lang('turnerof.tituprof')</label>
                        <div class="col-md-7">
                            <input id="titulo_universitario" type="text" class="form-control" name="titulo_universitario" value="{{ $titulos->titulo_universitario }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
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
                            <input id="titulo_prefijo" type="text" class="form-control" name="titulo_prefijo" value="{{ $titulos->titulo_prefijo }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('titulo_prefijo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('titulo_prefijo') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                                            
                    <!--estado
                    <div class="form-group col-xs-12{{ $errors->has('estado_titulo') ? ' has-error' : '' }}">
                        <label for="estado_titulo" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado_titulo" name="estado_titulo" class="form-control">
                                <option {{$titulos->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$titulos->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                            </select>  
                            @if ($errors->has('estado_titulo'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('estado_titulo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div> -->

                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4">
                            <button onclick="editar()" type="button" class="btn btn-success btn-gray">
                            @lang('agenda.actualizar')
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
<script>
  function editar() {
    var confirmar = confirm('¿seguro quiere editar?');
    if(confirmar) {
            $.ajax({
                url: "{{route('tituloprofesional.update')}}",
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
                            swal("Editado!", "trans('turnerof.correcto", "success");
                            window.location = url;
                        }, 3000);
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