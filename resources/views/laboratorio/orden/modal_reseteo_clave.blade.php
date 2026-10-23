<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">PASSWORD PACIENTE</h4>
</div>

<div class="modal-body">
  <div class="row" style="padding: 10px;">
      <form method="POST" id="form_reseteo_clave">
        {{ csrf_field() }}
        <input type="hidden" name="id_exa_orden" value="{{$id_exa_orden}}">
        <input type="hidden" name="id_usuario" value="{{$paciente->id_usuario}}">
        <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
        <div class="box-body col-md-12">
            <div class="form-group col-md-10 cl_password">
              <label for="password" class="col-md-3 control-label">Nuevo Password</label>
              <div class="col-md-8">
                <input id="password" type="password" class="form-control" name="password" required>
                <span class="help-block">
                  <strong id="str_password"></strong>
                </span> 
              </div>
            </div>
            <!--<div class="form-group col-md-6 cl_passwordconfirm">
              <label for="passwordconfirm" class="col-md-3 control-label">Confirmar Password</label>
              <div class="col-md-8">
                <input id="passwordconfirm" type="password" class="form-control" name="passwordconfirm" required>
                <span class="help-block">
                  <strong id="str_passwordconfirm"></strong>
                </span> 
              </div>
            </div>-->
          </div> 
        </form>
        <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
              <center>
                <div class="col-md-6 col-md-offset-2">
                  <img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 30%; display: none;">
                  <button  id="datos_clave" type="button" class="btn btn-primary" onclick="resetear_clave_paciente();">
                      Reseteo - Password
                  </button>
                </div>
              </center>
        </div>  
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
  

  function resetear_clave_paciente(){

    $('.cl_password').removeClass('has-error');
    $('#str_password').text('');
    $('#datos_clave').css("display", "none");
    $('#imagen_espera').css("display", "block");


    //$('.cl_passwordconfirm').removeClass('has-error');
    //$('#str_passwordconfirm').text('');

    $.ajax({
          type: 'post',
          url:"{{route('reseteo.clave')}}",
          headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
          datatype: 'json',
          data: $("#form_reseteo_clave").serialize(),
          success: function(data){
            console.log(data);
            if(data == "okay"){
              $('#reseteo_clave').modal('hide');
              $('#imagen_espera').css("display", "none");  
              $('#datos_clave').css("display", "block");
              //alert('Clave cambiada con Exito.');
              location.reload();
            }
      
          },
          error: function(data){
            if(data.responseJSON.password!=null){
              $('#datos_clave').css("display", "block");
              $('#imagen_espera').css("display", "none");
              $('.cl_password').addClass('has-error');
              $('#str_password').text(data.responseJSON.password);
            }
            /*if(data.responseJSON.passwordconfirm!=null){
              $('.cl_passwordconfirm').addClass('has-error');
              $('#str_passwordconfirm').text(data.responseJSON.passwordconfirm);
            }*/
          }
    });
  
  }

</script>

