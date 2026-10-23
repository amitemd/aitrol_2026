<!DOCTYPE HTML>
<html>
<head>
<title></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="{{ asset("/bower_components/AdminLTE/bootstrap/css/bootstrap.min.css") }}" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="{{asset('css/css_web/style.css')}}" type="text/css" media="all" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.css"/>

<script type="text/javascript" src="{{asset('js/js_web/jquery.js')}}"></script>
<script src="{{ asset ("/bower_components/jquery/dist/jquery.min.js")}}"></script>  
<script src="{{ asset ("/bower_components/AdminLTE/bootstrap/js/bootstrap.min.js") }}" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/6.11.0/sweetalert2.js"></script>

<style type="text/css">
    label{
        font-size: 12px;
    } 
    

    
</style>
</head>
<body>
    

    

    <div class="modal fade" id="orden" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div id="contenido_modal">
                
            </div>
        </div>
      </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <ul class="plan-list us_plan" style="width: 40%">
                <form name="hostingplan" id="guardar_externo" method="POST">
                    {{ csrf_field() }}
                    <input type="hidden" name="promo" id="promoid" value="">  
                    <li class="p-name2"><span class="pl-title">Ingreso del Paciente&nbsp;<b></b></span></li>
                    
                    <li class="p-feat1">
                        <!--cedula-->
                        <div class="form-group col-md-3">
                            <label for="id" class="control-label">Cédula</label>
                            <input id="id" maxlength="10" type="text" class="form-control input-sm" name="id" value="{{ old('id') }}" required autofocus autocomplete="off" onchange="buscapaciente();" >
                        </div>
                        <!--primer nombre-->
                        <div class="form-group col-md-3">
                            <label for="nombre1" class="control-label">Primer Nombre</label>
                            
                            <input id="nombre1" class="form-control input-sm" maxlength="40" type="text" name="nombre1" value="{{ old('nombre1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
                        </div>
                        <!--//segundo nombre-->
                        <div class="form-group col-md-3">
                            <label for="nombre2" class="control-label">Segundo Nombre</label>
                            <input id="nombre2" type="text" class="form-control input-sm nombrecode dropdown-toggle" maxlength="40" name="nombre2" value="{{ old('nombre2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="" readonly>
                        </div>
                        <!--primer apellido-->
                        <div class="form-group col-md-3">
                            <label for="apellido1" class="control-label">Primer Apellido</label>
                            
                            <input id="apellido1" type="text" class="form-control input-sm" maxlength="40" name="apellido1" value="{{ old('apellido1') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
                            
                        </div>
                        
                    </li>  
                    <li class="p-feat1">  
                        
                                     
                        <!--Segundo apellido-->
                        <div class="form-group col-md-3">
                            <label for="apellido2" class="control-label">Segundo Apellido</label>
                            <input id="apellido2" type="text" class="form-control input-sm nombrecode dropdown-toggle" maxlength="40" name="apellido2" value="{{ old('apellido2') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" autofocus required onchange="" readonly>
                        </div>

                        <!--sexo 1=MASCULINO 2=FEMENINO-->
                        <div class="form-group col-md-3{{ $errors->has('sexo') ? ' has-error' : '' }}">
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
                        <div class="form-group col-md-3">
                            <label class="control-label">Fecha Nacimiento</label>
                            <input type="date" value="{{old('fecha_nacimiento')}}" name="fecha_nacimiento" class="form-control pull-right input-sm" id="fecha_nacimiento" required onchange="" readonly>
                        </div>
                        <!--Celular-->
                        <div class="form-group col-md-3">
                            <label for="celular" class="control-label">Celular</label>
                            <input id="celular" type="text" class="form-control input-sm" maxlength="10" name="celular" value="{{ old('celular') }}" style="text-transform:uppercase;" onkeyup="javascript:this.value=this.value.toUpperCase();" required autofocus onchange="" readonly>
                            
                        </div>
                        <!--Direccion-->
                        <div class="form-group col-md-3">
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
                        <!--div class="form-group col-md-3">
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
            </ul>  
        </div>        
    </div>
    

    
       
            
                               
                
                    
                        
                       
                            
                                   
                                
                            
             
                            
                           
                      
                   
               
            
       
   
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
    <script type="text/javascript">
        $(document).ready(function(){

        });

        function buscar_orden(){
            var id_orden = $('#id_orden').val();
            $.ajax({
              type: 'get',
              url:"{{ url('laboratorio/externo/web/promo/buscar/numero') }}/"+id_orden, 
              
              success: function(xdata){
                //console.log(xdata);
                $('#contenido_modal').empty().html(xdata);  
                $("#orden").modal();
              },
              error: function(xdata){
                //console.log(xdata);
              }
            });
        }   

        function ingreso_orden(id){
            $.ajax({
                type: 'get',
                url: "{{ url('laboratorio/externo/web/promo')}}/"+id, //lab_externo.promo
                           
                success: function(data){
                    if(id =='1'){
                        $('#ul2').css("display","none");
                        $('#ul3').css("display","none");
                        $('#ul4').css("display","none");
                        $('#ul5').css("display","none");
                        $('#ulx').css("display","inline-block");    
                    };
                    if(id =='2'){
                        $('#ul1').css("display","none");
                        $('#ul3').css("display","none");
                        $('#ul4').css("display","none");
                        $('#ul5').css("display","none");
                        $('#ulx').css("display","inline-block");    
                    };
                    if(id =='3'){
                        $('#ul2').css("display","none");
                        $('#ul1').css("display","none");
                        $('#ul4').css("display","none");
                        $('#ul5').css("display","none");
                        $('#ulx').css("display","inline-block");    
                    };
                    if(id =='4'){
                        $('#ul2').css("display","none");
                        $('#ul1').css("display","none");
                        $('#ul3').css("display","none");
                        $('#ul5').css("display","none");
                        $('.domi').css("display","none");
                        $('.labs').css("display","inline-block");
                        $('#ulx').css("display","inline-block");    
                    };
                    if(id =='5'){
                        $('#ul2').css("display","none");
                        $('#ul1').css("display","none");
                        $('#ul3').css("display","none");
                        $('.labs').css("display","none");
                        $('.domi').css("display","inline-block");
                        $('#ulx').css("display","inline-block");    
                    };

                    $('#ulx').empty().html(data);
                    
                }    
            }); 
        }

    </script>

<script type="text/javascript">
    function pagar_orden(){ 
        $('#bpago').prop('disabled', true);  
        var email = $('#email').val();
        var texto = '';
        if(email==''){
            texto = "Ingrese el email";
        }
        if(texto != ''){
            $("#err").text(texto);
            swal.fire({
                title: texto,
                //text: "You won't be able to revert this!",
                icon: "error",
                type: 'error',
                buttons: true,
              
            })    
        }
        
        if (texto=='') { 
            swal.fire({
                title: 'Confirme desea realizar el pago',
                //text: "You won't be able to revert this!",
                icon: "success",
                type: 'success',
                buttons: true,
              
            }).then((result) => {
              if (result.value) {
                $('#imagen_espera2').css("display", "block"); //En proceso 
                $("#bpago").css("display","none"); 
                $.ajax({

                    type: "post",
                        url: "{{route('lab_externo.pagar_orden')}}",
                        headers:{'X-CSRF-TOKEN':$('input[name=_token]').val()},
                        datatype: "json",
                        data: $("#guardar_mail").serialize(),
                        success: function(datahtml){
                            console.log(datahtml);
                            if(datahtml.estado=='ok'){
                                $("#bpago").css("display","none");
                                envio_mail(datahtml.paciente,datahtml.usuario);
                                //$(".alerta_ok").fadeIn(1000);
                                //$(".alerta_ok").fadeOut(20000);
                                /*swal({
                                    title: "Pago realizado con éxito.",
                                    icon: "success",
                                    type: 'success',
                                    buttons: true,
                                });*/
                                
                            }

                        },
                        error: function(datahtml){

                            console.log(JSON.parse(datahtml.responseText).email[0]);
                            var err = JSON.parse(datahtml.responseText).email[0];
                            if(err!=null){
                                $("#err").text(err);
                                var txt_er = err;
                                //$(".alerta_correcto").fadeIn(1000);
                                //$(".alerta_correcto").fadeOut(10000);
                            }else{
                                var txt_er = 'No se pudo realizar pago, por favor comuniquese con la administración';
                            }
                            swal.fire({
                                title: txt_er,
                                //text: "You won't be able to revert this!",
                                icon: "error",
                                type: 'error',
                                buttons: true,
                              
                            }).then((result) => {
                              if (result.value) {
                                refresh();
                              }
                            })


                        }
                });
              }
            })
            
           
        }  
    }
    function refresh(){
        location.reload();
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
                    $('#id_orden').val('');
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
                    $('#id_orden').val('');
                    refresh();
                  }
                })
                //refresh();


                }  
        });     
    }
                                
      
</script>

    
    


    <script type="text/javascript">
        function openModal(modalId){
            $('#modal_pago_1').show();
        }
        function closeModal(modalId){
            $('#modal_pago_1').hide();
        }


    </script>
    
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
</body>
</html>
