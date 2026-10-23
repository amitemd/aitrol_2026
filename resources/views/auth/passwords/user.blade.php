@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Olvido su Correo</div>
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form id="recuperacion" class="form-horizontal" role="form" method="POST" action="{{ route('auth.user.recover') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('cedula') ? ' has-error' : '' }}">
                            <label for="cedula" class="col-md-4 control-label">Ingrese su Cedula</label>

                            <div class="col-md-6">
                                <input id="cedula" type="text" class="form-control" name="cedula" value="{{ old('cedula') }}" required>

                                @if ($errors->has('cedula'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('cedula') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('fecha') ? ' has-error' : '' }}">
                            <label for="fecha" class="col-md-4 control-label">Ingrese su fecha de Nacimiento</label>

                            <div class="col-md-6">
                                <input id="fecha" type="date" class="form-control" name="fecha" value="{{ old('fecha') }}" required>

                                @if ($errors->has('fecha'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('fecha') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="button" onclick="verificar()" class="btn btn-primary">
                                    Recuperar
                                </button>
                            </div>
                        </div>
                    </form>
                    <div id="area_trabajo" class="form-group" style="text-align:center;">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function verificar(){
        var formulario = document.forms["recuperacion"];
        var cedula = formulario.cedula.value;
        var fecha = formulario.fecha.value;
        var msj = "";

        if(cedula == "")
            msj += "Por favor, ingrese su cedula\n";
        if(fecha == "")
            msj += "Por favor, ingrese su fecha de nacimiento\n";
        if(msj == false)
        {
            $.ajax({
                type:'post',
                url:"{{route('auth.user.recover')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#recuperacion").serialize(),
                success: function(data){
                  $("#area_trabajo").html(data);
                  console.log(data);
                },
                error:  function(){
                    alert('error al cargar');
                }
            });
        }
        else
            alert(msj);


    }
</script>
@endsection
