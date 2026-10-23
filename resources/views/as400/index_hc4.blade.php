<section class="content">
    <div class="box " style="background-color: white;">
        <div class="box-header with-border" style="color: black; font-family: 'Helvetica general3';border-bottom: #3c8dbc; ">
            <div class="col-md-9">
              <h3 class="box-title">Agregar Orden de Laboratorio AS400</h3>
            </div>
        </div>
        <div class="box-body" style="background-color: #ffffff;">

            <div class="box-body col-xs-12">
                <form class="form-vertical" role="form" method="POST" action="{{ route('producto.store') }}">
                    {{ csrf_field() }}
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
                            <button type="button" onclick="validarAs400()" id="validar_boton_as400" class="btn btn-primary" data-loading-text="<i class='fa fa-spinner fa-spin'></i> Validando Orden" >
                                Comprobar Codigo
                            </button>
                        </div>
                    </div>
                </form>

                <div id="contenedor_resultados" >

                </div>
            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    function validarAs400(){
        var codigo = $('#codigo').val();
        $('#validar_boton_as400').hide();

        $.ajax({
            type: 'post',
            url:"{{route('as400.validar_codigo')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'html',
            data: $("#codigo"),
            success: function(data){
                if(data.toString().trim() == "ok"){
                    carga_ordenes_laboratorio();
                }else{
                    $('#validar_boton_as400').show();
                    $('#contenedor_resultados').html(data);
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    }
</script>
