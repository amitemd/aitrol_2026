<div class="modal-header">
  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" style="color:red; font-size: 50px; font-weight: bolder;">×</span></button>
  <h4 class="modal-title" id="myModalLabel" style="text-align: center;">DATOS PACIENTE</h4>
</div>

<div class="modal-body">
  <div class="row" style="padding: 10px;">
      <form method="POST" id="form_pago">
        {{ csrf_field() }}
        <input type="hidden" name="id_exa_orden" value="{{$id_exa_orden}}">
        <input type="hidden" name="id_usuario" value="{{$user_aso->id}}">
        <input type="hidden" name="id_paciente" value="{{$paciente->id}}">
           
          <div class="box-body col-md-12">
            <!--primer nombre-->
            <div class="form-group col-md-6" style="padding: 0;">
              <label for="nombre1" class="col-md-3 control-label">Nombres:</label>
              <div class="col-md-8">
                @if(!is_null($paciente->nombre1))
                  {{$paciente->nombre1}} @if($paciente->nombre2!='(N/A)'){{$paciente->nombre2}}@endif
                @endif
              </div>
            </div>
            <!--primer apellido-->
            <div class="form-group col-md-6" style="padding: 0;">
              <label for="apellido1" class="col-md-3 control-label">Apellidos:</label>
              <div class="col-md-8">
                @if(!is_null($paciente->apellido1))
                  {{$paciente->apellido1}} @if($paciente->apellido2!='(N/A)'){{$paciente->apellido2}}@endif
                @endif  
              </div>
            </div>
            <!--email-->  
            @php
              $leer = false;
              if($paciente->id != $user_aso->id){
                $leer = true; 
              }
            @endphp
            <div class="form-group col-md-6 cl_email" style="padding: 0;">
              <label for="email" class="col-md-3 control-label">E-Mail:</label>
              <div class="col-md-7">
                <input class="form-control" type="email" name="email" id="email" value="{{$user_aso->email}}" required @if($leer) readonly @endif onchange="busca_mail();"> 
                <span class="help-block">
                  <strong id="str_email"></strong>
                </span>
              </div>
              <div class="col-md-1">
                <button type="button" class="btn btn-success btn-xs"><span class="glyphicon glyphicon-search"></span></button>    
              </div>  
            </div>
            <!--cedula-->
            <div class="form-group col-md-6" style="padding: 0;">
              <label for="id" class="col-md-3 control-label">Cedula:</label>
              <div class="col-md-8">
                @if(!is_null($paciente->id))
                  {{$paciente->id}} 
                @endif
              </div>
            </div>
          </div> 
        </form>
        <div style="padding-top: 10px;padding-left: 70px" class="form-group col-md-12">
              <center>
                <div class="col-md-4 ">
                  <img id="imagen_espera" src="{{asset('/images/espera.gif')}}" style="width: 30%; display: none;">
                  <button id="enviar_datos" type="button" class="btn btn-warning" onclick="actualiza_email_paciente();">
                      Emisión y Envio de Correo
                  </button>
                </div>
              </center>
        </div>
        @if($leer)<div class="form-group col-md-12 alert-success"><h3>Principal: {{$user_aso->apellido1}} {{$user_aso->apellido2}} {{$user_aso->nombre1}} {{$user_aso->nombre2}}</h3></div>@endif
        <!--<div style="padding-top: 10px" class="form-group col-md-12 col-md-offset-5" class="close" data-dismiss="modal">

        </div>-->
        <div class="form-group col-md-12 alert-success oculto" id="mensaje"></div>
  </div>
</div>
<div class="modal-footer">
  <button type="button" class="btn btn-default" data-dismiss="modal" >Cerrar</button>
</div>

<script type="text/javascript">
  

  function actualiza_email_paciente(){
    var email = $('#email').val();
    if(email != ''){
      swal.fire({
        title: 'Al emitir la orden, se enviará a laboratorio con la fecha de emisión {{date('Y-m-d')}} y por correo al paciente el enlace para que acceda al sistema',
        //text: "You won't be able to revert this!",
        icon: "warning",
        type: 'warning',
        showCancelButton: true,
        cancelButtonColor: '#d33',
        buttons: true,
      
      }).then((result) => {
        if (result.value) {
          $('.cl_email').removeClass('has-error');
          $('#str_email').text('');
          $('#enviar_datos').css("display", "none");
          $('#imagen_espera').css("display", "block");

          //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}"; 
          setTimeout(emite_final(), 5000);

          $.ajax({
                type: 'post',
                url:"{{route('update.estado_email_pago')}}",
                headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                datatype: 'json',
                data: $("#form_pago").serialize(),
                success: function(data){
                  console.log("mail:"+data);
                  if(data == "okay"){
                    $('#pago').modal('hide');
                    $('#imagen_espera').css("display", "none");  
                    $('#enviar_datos').css("display", "block");
                    //alert('Envio de Correo Exitoso.');
                    
                  }
                  //////
                  //cierre_caja()
                  //location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";                
                  ///////
                },
                error: function(data){
                  if(data.responseJSON.email!=null){
                    $('#enviar_datos').css("display", "block");
                    $('#imagen_espera').css("display", "none");  
                    $('.cl_email').addClass('has-error');
                    $('#str_email').text(data.responseJSON.email);
                  }else{
                  	//cierre_caja()
                  	//location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";

                  }
                }
          });
          
        }
      });
    }else{
      alert("Ingrese el mail");
    }  
  
  }

  function emite_final(){
    location.href = "{{route('cotizador.generar',['id' => $orden->id])}}";   
  }

  function busca_mail(){
        var email = document.getElementById('email').value;
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/mail/principal')}}/"+email, //orden.recupera_mail
                       
            success: function(data){
                if(data=='no'){
                    /*$('#nombre1').val('');
                    $('#nombre2').val('');
                    $('#apellido1').val('');
                    $('#apellido2').val('');
                    $('#sexo').val('');
                    $('#fecha_nacimiento').val('1980/01/01');*/
                }else{
                    //console.log(data);
                    $('#mensaje').removeClass('oculto');
                    $('#mensaje').empty().html('El correo pertenece a '+data.apellido1+' '+data.apellido2+' '+data.nombre1+' '+data.nombre2+' con CI: '+data.id+', si ingresa el paciente quedará anexado como grupo familiar');
                }
            }    
        }); 
    } 

  

</script>

