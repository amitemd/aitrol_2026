@extends('actas_de_entrega.base')
@section('action-content')

<section class="content">
    <div class="box">
        <div class="box-header"><h3 class="box-title">Crear Acta</h3>
        </div>
        <div class="box-body">
            <form id="crear_acta" method="post" action="{{route('acta.store_acta')}}" onclick=imeiDisplay()>
                {{ csrf_field() }}
                                         
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('usuario_entrega') ? ' has-error' : '' }}">
                        <label for="usuario_entrega" class="col-md-20 control-label">Usuario entrega</label>
                        <input type="text" id="usuario_entrega" name="usuario_entrega" style="text-transform: uppercase" size="40" class="form-control input-sm" value="" autocomplete="off"
                         required maxlength="100">
                    </div>
                </div>
                
                <input type="hidden" name="id_usuario_entrega" id="id_usuario_entrega" value="">

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('usuario_recibe') ? ' has-error' : '' }}">
                        <label for="usuario_recibe" class="col-md-20 control-label">Usuario recibe</label>
                        <input type="text" id="usuario_recibe" name="usuario_recibe" size="40" style="text-transform: uppercase" class="form-control input-sm" value="" autocomplete="off">
                    </div>
                </div>
                
                <input type="hidden" name="id_usuario_recibe" id="id_usuario_recibe" value="">

                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('tipo') ? ' has-error' : '' }}">
                        <label for="tipo" class="col-md-20 control-label">Tipo</label>
                        <!--<input type="text" name="tipo" size="40" class="form-control" value=" " >-->
                        <select id='select_tipo' class="form-control input-sm" name="select_tipo" class="form-control" required>
                            <option value="">Seleccione...</option>
                            @foreach($tipo_actas as $tipo_acta)
                            <option value="{{$tipo_acta->id}}">{{$tipo_acta->nombre}}</option>
                            @endforeach
                      </select> 
                    </div>
                </div>
                
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('descripcion') ? ' has-error' : '' }}">
                        <label for="descripcion" id="label_descripcion" class="col-md-20 control-label">Descripción</label>
                        <input type="text" id="descripcion" name="descripcion" size="100" class="form-control" placeholder="e.g. Poco X3 Pro" value="" required>
                    </div>
                </div>
                
                
                <div class="box-body">
                    <div class="form-group col-xs-6{{ $errors->has('info_adicional') ? ' has-error' : '' }}">
                        <label for="info_adicional" id="label_info_adicional" class="col-md-20 control-label">Información adicional</label>
                        <input type="text" id="info_adicional" name="info_adicional" size="100" class="form-control" placeholder="e.g. cargador y protector" value="">
                    </div>
                </div>

                
                <div class="box-body" id="input_imei" style="display: none">
                    <div class="form-group col-xs-6{{ $errors->has('imei') ? ' has-error' : '' }}">
                        <label for="imei" id="label_imei" class="col-md-20 control-label">IMEI</label>
                        <input type="text" id="imei" name="imei" size="100" class="form-control" minlength="15" maxlength="31" placeholder="e.g. 012345678912345 012345678912345" value="" required>
                    </div>
                </div>

                <div class="box-body" id="input_codigo" style="display: none">
                    <div class="form-group col-xs-6{{ $errors->has('codigo') ? ' has-error' : '' }}">
                        <label for="codigo" id="label_codigo" class="col-md-20 control-label">Código tarjeta</label>
                        <input type="text" id="codigo" name="codigo" size="100" class="form-control" placeholder="0123456789 12345678" value="" required>
                    </div>
                </div>

                <div class="box-body" id="input_modelo" style="display: none">
                    <div class="form-group col-xs-6{{ $errors->has('modelo') ? ' has-error' : '' }}">
                        <label for="modelo" id="label_modelo" class="col-md-20 control-label">Modelo</label>
                        <input type="text" id="modelo" name="modelo" size="100" class="form-control" placeholder="Thinkbook 15-IML" value="" required>
                    </div>
                </div>


                <div class="box-body" id="input_celular" style="display: none">
                    <div class="form-group col-xs-6{{ $errors->has('numero_cel') ? ' has-error' : '' }}">
                        <label for="numero_cel" id="label_numero_cel" class="col-md-20 control-label">Número celular</label>
                        <input type="tel" id="numero_cel" name="numero_cel" minlength="10" maxlength="10" pattern="^[0-9]+$" placeholder="0123456789" class="form-control" value="" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="col-md-6 col-md-offset-4">
                        <button type="submit" class="btn btn-primary">
                            Agregar
                        </button>
                    </div>
                </div>

            </form>
        </div>
        
    </div>
</section>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>

$("#usuario_entrega").autocomplete({
    source: function( request, response ) {

      $.ajax({
          url:"{{route('acta.user_nombre')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {
              term: request.term
                },
          dataType: "json",
          type: 'post',
          success: function(data){
              response(data);
              console.log(data);
          }
      })
    },
    minLength: 2,
  } );


  $("#usuario_entrega").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('acta.user_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#usuario_entrega"),
            success: function(data){
                console.log(data);
                if(data!='0'){
                    $('#id_usuario_entrega').val(data.id);

                }

            },
            error: function(data){

                }
        })
    });

    $("#usuario_recibe").autocomplete({
    source: function( request, response ) {

      $.ajax({
          url:"{{route('acta.user_nombre')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          data: {
              term: request.term
                },
          dataType: "json",
          type: 'post',
          success: function(data){
              response(data);
              console.log(data);
          }
      })
    },
    minLength: 2,
  } );


  $("#usuario_recibe").change( function(){
        $.ajax({
            type: 'post',
            url:"{{route('acta.user_nombre2')}}",
            headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
            datatype: 'json',
            data: $("#usuario_recibe"),
            success: function(data){
                console.log(data);
                if(data!='0'){
                    $('#id_usuario_recibe').val(data.id);
                }

            },
            error: function(data){

                }
        })
    });


    function imeiDisplay() {
        var valorSelect = $('#select_tipo').val();
        celu = $("#input_celular");
        cod = $("#input_codigo");
        mod = $("#input_modelo");
        ime = $("#input_imei");

       if (valorSelect == 1) {
        ime.show();
        celu.show();
        cod.hide();
        mod.hide();
        document.getElementById('codigo').required = false;
        document.getElementById('modelo').required = false;
         
       } else if (valorSelect == 2) {
        ime.hide();
        celu.hide();
        cod.show();
        mod.hide();
        document.getElementById('numero_cel').required = false;
        document.getElementById('modelo').required = false;
        document.getElementById('imei').required = false;
        

        } else if (valorSelect == 3) {
        ime.hide();
        celu.hide();
        cod.hide();
        mod.show();
        document.getElementById('numero_cel').required = false;
        document.getElementById('codigo').required = false;
        document.getElementById('imei').required = false;
        

       }
    }

</script>

@endsection