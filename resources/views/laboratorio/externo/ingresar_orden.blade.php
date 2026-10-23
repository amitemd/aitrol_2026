<style type="text/css">
    label{
        font-size: 12px;
    }
    .alerta_correcto{
      position: fixed;
      z-index: 9999;
      bottom:  58%;
      left: 41%;
      
    }
    .alerta_ok{
      position: fixed;
      z-index: 9999;
      bottom:  58%;
      left: 41%;
      
    }
    
</style>
<style>
	.modal-payment-wrapper{
		position:fixed;
		width:100%;
		top:0px;
		left:0px;
		height:100%;
		background-color:rgba(0, 0, 0, 0.9);
		text-align:center;
		overflow:auto;
        z-index:999999;
	}		
	.modal-payment-wrapper .modal-payment{
		background:#FFFFFF;
		height:1700px;
		padding:0px;
	}		
	.modal-payment-wrapper .modal-payment .modal-title{
		text-align: right;
		padding: 15px;
		background:#DDD;
		border-bottom: solid 1px #CCC;
	}			
	.modal-payment-wrapper .modal-payment .modal-title .closex{
		width:20px;
		height:auto;
		cursor:pointer;
	}
	.modal-payment-wrapper .modal-payment .modal-title .closex:hover{
		opacity:0.5;
	}	
	.modal-payment-wrapper .modal-payment iframe{
		width:100%;
		border:none;
		height:1601px;
	}
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css"/>


<script type="text/javascript">
	function openModal(modalId){
		$('#modal_pago_1').show();
	}
	function closeModal(modalId){
		$('#modal_pago_1').hide();
	}
</script>
<div class="alert alert-danger alerta_correcto alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
    <b>Complete los datos: <span id="err"></span></b>
</div>
<div class="alert alert-success alerta_ok alert-dismissable col-6" role="alert" style="display: none;font-size: 14px;">
    <b>Paquete ingresado correctamente</b>
</div>

<form name="hostingplan" id="guardar_externo" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="promo" id="promoid" value="{{$id}}">  
    <li class="p-name2"><span class="pl-title">Ingreso del Paciente&nbsp;<b></b></span></li>
    
    <li class="p-feat1">
        <!--cedula-->
        <div class="form-group col-md-4">
            <label for="id" class="control-label">Cédula</label>
            <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" required autofocus autocomplete="off" onchange="buscapaciente();" >
        </div>
        <!--primer nombre-->
        <div class="form-group col-md-4">
            <label for="nombre1" class="control-label">Primer Nombre</label>
            
            <input id="nombre1" class="form-control input-sm" maxlength="40" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
        </div>
        <!--//segundo nombre-->
        <div class="form-group col-md-4">
            <label for="nombre2" class="control-label">Segundo Nombre</label>
            <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" maxlength="40" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="" readonly>
        </div>
        
    </li>  
    <li class="p-feat1">  
        <!--primer apellido-->
        <div class="form-group col-md-4">
            <label for="apellido1" class="control-label">Primer Apellido</label>
            
            <input id="apellido1" type="text" class="form-control input-sm" maxlength="40" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
            
        </div>
                     
        <!--Segundo apellido-->
        <div class="form-group col-md-4">
            <label for="apellido2" class="control-label">Segundo Apellido</label>
            <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" maxlength="40" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="" readonly>
        </div>

        <!--sexo 1=MASCULINO 2=FEMENINO-->
        <div class="form-group col-md-4{{ $errors->has('sexo') ? ' has-error' : '' }}">
            <label for="sexo" class="control-label">Sexo</label>
            <select id="sexo" name="sexo" class="form-control input-sm" required onchange="" readonly>
                <option value="">Seleccionar ..</option>
                <option value="1">MASCULINO</option>
                <option value="2">FEMENINO</option>
                        
            </select>       
        </div>  

    </li>  

    <li class="p-feat1">
        <!--fecha_nacimiento-->
        <div class="form-group col-md-4">
            <label class="control-label">Fecha Nacimiento</label>
            <input type="date" value="{{old('fecha_nacimiento')}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required onchange="" readonly>
        </div>
        <!--Celular-->
        <div class="form-group col-md-4">
            <label for="celular" class="control-label">Celular</label>
            <input id="celular" type="text" class="form-control input-sm" maxlength="10" name="celular" value="{{ old('celular') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
            
        </div>
        <!--Direccion-->
        <div class="form-group col-md-4">
            <label for="direccion" class="control-label">Dirección</label>
            
            <input id="direccion" type="text" class="form-control input-sm" name="direccion" maxlength="70" value="{{ old('direccion') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
            
        </div>

    </li> 

    <li class="p-feat1">
        
        <!--email-->
        <div class="form-group col-md-8">
            <label for="email" class="control-label">E-mail</label>
            
            <input id="email" type="text" class="form-control input-sm" name="email" maxlength="70" value="{{ old('email') }}" required autofocus onchange="" >
            @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
            @endif
            
        </div>
        <!--email-->
        <!--div class="form-group col-md-4">
            <label for="forma_pago" class="control-label">Forma de Pago</label>
            <select id="forma_pago" name="forma_pago" class="form-control input-sm" required onchange="" >
                <option value="1">EFECTIVO/CHEQUE</option>
                <option value="2">TARJETA DEBITO</option> 
                <option value="2">TARJETA CREDITO</option>      
            </select>   
        </div-->

        

    </li>  

    
    <li class="p-feat1"><br></li> 
    <li class="p-feat1"><br></li>     
    <li class="p-feat" id="bingresa" >
        <a class="btn btn-primary btn-xs" id="btnpago" onclick="ingreso_orden();return false;"><i class="glyphicon glyphicon-credit-card"> </i> Comprar</a>
    </li> 
    <li class="p-feat" id="imagen_espera"  style="display: none;">
        <img src="{{asset('/images/espera.gif')}}" style="width: 10%;"> En proceso ...
    </li> 
    <li class="p-feat" id="brefresca" ><a class="btn btn-warning btn-xs" onclick="refresh();return false;"><i class="glyphicon glyphicon-arrow-left"> </i> Regresar</a></li> 

                        

</form>

<div id="modal_pago_1" class="modal-payment-wrapper" style="display:none">					
		<div class="modal-payment container">
			<div class="modal-title">
				<h2 style="float: left;width: 250px;text-align: left;font-size: 1.2em;padding-left: 25px;text-transform: uppercase; color:#000000">Pago en línea</h2>
				<img src="/sis_medico/public/images/close.png" title="cerrar" class="closex" onclick="closeModal('modal_pago_1')"/>
			</div>
			<iframe id="sanbox_pago" src="https://vpos.accroachcode.com/"></iframe>
		</div>
	</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script type="text/javascript">
    var buscapaciente = function ()
    {
    
        var js_paciente = document.getElementById('id').value;
        
        $.ajax({
            type: 'get',
            url: "{{ url('laboratorio/externo/web/promo/buscarpaciente')}}/"+js_paciente, 
                       
            success: function(data){
                console.log(data);
                if(data=='no'){
                    
                    $('#nombre1').removeAttr("readonly");
                    $('#nombre2').removeAttr("readonly");
                    $('#apellido1').removeAttr("readonly");
                    $('#apellido2').removeAttr("readonly");
                    $('#sexo').removeAttr("readonly");
                    $('#fecha_nacimiento').removeAttr("readonly");
                    $('#celular').removeAttr("readonly");
                    $('#direccion').removeAttr("readonly");
                    $('#email').removeAttr("readonly");
                    
                    
                 
                }else{
                    //alert('Paciente ya ingresado en el sistema');
                    //console.log(data);
                    $('#nombre1').val(data.nombre1);
                    $('#nombre2').val(data.nombre2);
                    $('#apellido1').val(data.apellido1);
                    $('#apellido2').val(data.apellido2);
                    $('#sexo').val(data.sexo);
                    $('#fecha_nacimiento').val(data.fecha_nacimiento);
                    $('#celular').val(data.telefono1);
                    $('#direccion').val(data.direccion);
                    $('#email').val(data.email);
                    //$('#procedencia').focus();
                }
            }    
        });  
    
    }

    function ingreso_orden(){
        
        $('#btnpago').prop('disabled', true);
        var cedula = $('#id').val();
        var nombre1 = $('#nombre1').val();
        var apellido1 = $('#apellido1').val();
        var sexo = $('#sexo').val();
        var fecha_nacimiento = $('#fecha_nacimiento').val();
        var celular = $('#celular').val();
        var direccion = $('#direccion').val();
        var email = $('#email').val();
        var texto = '';
        //alert(cedula);
        if(cedula==''){
            texto = texto + " cedula";
        }
        if(nombre1==''){
            texto = texto + " nombre";
        }
        if(apellido1==''){
            texto = texto + " apellido";
        }
        if(sexo==''){
            texto = texto + " sexo";
        }
        if(fecha_nacimiento==''){
            texto = texto + " fecha de nacimiento";
        }
        if(celular==''){
            texto = texto + " celular";
        }
        if(direccion==''){
            texto = texto + " direccion";
        }
        if(email==''){
            texto = texto + " email";
        }
        //console.log(texto);
        if(texto != ''){

            $("#err").text(texto);
            swal.fire({
                title: 'complete los siguientes campos:'+texto,
                //text: "You won't be able to revert this!",
                icon: "error",
                type: 'error',
                buttons: true,
              
            })    
        }
        if (texto=='') {
            
                $('#imagen_espera').css("display", "block"); //En proceso
                
                $.ajax({// Guarda en Base
                    type: "post",
                    url: "{{route('lab_externo.guardar')}}",
                    headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                    datatype: "json",
                    data: $("#guardar_externo").serialize(),
                    success: function(datahtml){
                        //vpos invocation here -----------------------------------------                    
                        $('#sanbox_pago').attr('src',datahtml.url_vpos);
                        openModal('modal_pago_1');
                        //---------------------------------------------------------------
                        if(datahtml.estado=='ok'){            
                            //DESCOMENTAR O COLOCAR EN LA COMPRA EXITOSA
                            //envio_mail(cedula,datahtml.usuario);                             
                            $("#bingresa").css("display","none");                            
                        }  
                    },
                    error: function(datahtml){
                        //console.log(JSON.parse(datahtml.responseText).email[0]);
                        $('#imagen_espera').css("display", "none");
                        var err = JSON.parse(datahtml.responseText).email[0];
                        if(err!=null){
                            $("#err").text(err);
                            var txt_err = err;
                        
                        }else{
                            var txt_err = 'Existió un problema con su pago, por favor comuniquese con administración'
                        }
                        swal.fire({
                            title: txt_err,
                            //text: "You won't be able to revert this!",
                            icon: "error",
                            type: 'error',
                            buttons: true,
                          
                        })  
                    }
                });
                

        }       
        
    }


    function envio_mail(cedula,usuario){
        $.ajax({
            type: 'get',
            url: "{{ url('mail/laboratorio/externo')}}/"+cedula+"/"+usuario, 
                       
            success: function(data){
                //$(".alerta_ok").fadeIn(1000);
                //$(".alerta_ok").fadeOut(20000);
                //console.log(data);
                $('#imagen_espera').css("display", "none");
                $("#brefresca").css("display","inline-block");
                
                swal.fire({
                    title: 'Pago realizado con éxito. Se envió un correo con su acceso al sistema',
                    //text: "You won't be able to revert this!",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                  
                }).then((result) => {
                  if (result.value) {
                    refresh();
                  }
                })
                //refresh();
                
            },  

            error: function(data){
                //$(".alerta_ok").fadeIn(1000);
                //$(".alerta_ok").fadeOut(20000);
                $('#imagen_espera').css("display", "none");
                $("#brefresca").css("display","inline-block");
                swal.fire({
                    title: 'Pago realizado con éxito. No se pudo enviar el correo, comuniquese con la administrtación',
                    //text: "You won't be able to revert this!",
                    icon: "success",
                    type: 'success',
                    buttons: true,
                  
                }).then((result) => {
                  if (result.value) {
                    refresh();
                  }
                })
                //refresh();


                }  
        });     
    }
    function refresh(){
        location.reload();
    }


    
</script>

