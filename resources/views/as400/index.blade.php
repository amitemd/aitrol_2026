@extends('as400.base')
@section('action-content')

<script type="text/javascript">
    function check(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla == 8) {
            return true;
        }

        // Patron de entrada, en este caso solo acepta numeros y letras
        patron = /[A-Za-z0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    }

    function goBack() {
      window.history.back();
    }

</script>
<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title">Agregar Orden de Laboratorio AS400</h3>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <button onclick="goBack()" class="btn btn-danger" style="color:white; background-color: #3c8dbc; border-radius: 5px; border: 2px solid white;">
                   <i class="glyphicon glyphicon-arrow-left" aria-hidden="true"></i>&nbsp;&nbsp;Regresar
                </button>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">
            <form class="form-vertical" role="form" method="POST" action="{{ route('producto.store') }}">
                {{ csrf_field() }}
                <div class="box-body col-xs-24">
                    <!--Codigo-->
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" class="col-md-4 control-label">Codigo IESS AS400</label>
                        <div class="col-md-7">
                            <input id="codigo" type="text" class="form-control" name="codigo" value="{{ old('codigo') }}" style="text-transform:uppercase;"  maxlength="25"  required autofocus >
                            @if ($errors->has('codigo'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('codigo') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="form-group col-xs-6" style="text-align: center;">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="button" onclick="validarAs400()" id="validar_boton" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Validando Orden" >
                                Comprobar Codigo
                            </button>
                        </div>
                    </div>

                    <div id="contenedor_resultados" >

                    </div>
                </div>
            </form>
        </div>
    </div>
</section>

<script type="text/javascript">
    function validarAs400(){
        var codigo = $('#codigo').val();
        $('#validar_boton').button('loading');
        $.ajax({
            type: 'post',
            url:"{{route('as400.validar_codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'html',
            data: $("#codigo"),
            success: function(data){
                $('#contenedor_resultados').html(data);
                $('#validar_boton').button('reset');
            },
            error: function(data){
                console.log(data);
                $('#validar_boton').button('reset');
            }
        });
    }
</script>
@endsection
