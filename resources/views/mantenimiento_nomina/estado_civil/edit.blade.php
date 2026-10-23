@extends('mantenimiento_nomina.estado_civil.base')
@section('action-content')
<div class="container">
    <div class="row">
        <div class="box box-primary col-xs-24">
            <div class="box-header"><h3 class="box-title">Editar Estado Civil</h3></div>
             <form class="form-vertical" role="form" id="formulario">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <input type="hidden" name="_method" value="PATCH">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="id" name="id" value="{{$id}}">       
                        
                    <!--Nombre del area-->
                    <div class="form-group col-xs-12{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" class="col-md-2 control-label">Estado Civil</label>
                        <div class="col-md-7">
                            <input id="descripcion" type="text" class="form-control" name="descripcion" value="{{ $mantenimientos_estadocivil->descripcion }}"  style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();"required autofocus>
                            @if ($errors->has('descripcion'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('descripcion') }}</strong>
                                </span>
                            @endif
                        </div>    
                    </div>
                                            
                    <!--estado-->
                    <div class="form-group col-xs-12{{ $errors->has('estado_civil') ? ' has-error' : '' }}">
                        <label for="estado_civil" class="col-md-2 control-label">Estado</label>
                        <div class="col-md-7">
                            <select id="estado_civil" name="estado_civil" class="form-control">
                                <option {{$mantenimientos_estadocivil->estado == 0 ? 'selected' : ''}} value="0">INACTIVO</option>
                                <option {{$mantenimientos_estadocivil->estado == 1 ? 'selected' : ''}} value="1">ACTIVO</option>            
                            </select>  
                            @if ($errors->has('estado_civil'))
                            <span class="help-block">
                                    <strong>{{ $errors->first('estado_civil') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>    

                    <div class="form-group col-xs-6">
                        <div class="col-md-6 col-md-offset-4" align="center">
                            <button onclick="editar()" type="button" class="btn btn-success btn-gray">
                            Actualizar
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

        $.ajax({
            url: "{{route('estado_civil.update')}}",
            headers: {
                'X-CSRF-TOKEN': $('input[name=_token]').val()
            },
            data: $('#formulario').serialize(),
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                var url = "{{route('estado_civil.index')}}"
                if (data == 'ok') {
                    setTimeout(function() {
                        swal("Editado!", "Correcto", "success");
                        window.location = url;
                    }, 3000);
                }
            },
            error: function(xhr, status) {
                alert('Existió un problema');
                //console.log(xhr);
            },
        });
    }  
</script>
@endsection